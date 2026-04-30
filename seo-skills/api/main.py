"""
SEO Blogger Toolkit - REST API.

Standalone FastAPI server exposing the toolkit's SEO scripts as HTTP endpoints.
Run with:
    uvicorn api.main:app --reload --port 8000
"""

from __future__ import annotations

import os
import sys
from pathlib import Path

from fastapi import FastAPI, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware

# Make ../scripts importable as a flat module path.
_API_DIR = Path(__file__).resolve().parent
_PROJECT_ROOT = _API_DIR.parent
sys.path.insert(0, str(_PROJECT_ROOT / "scripts"))

import fetch_page  # noqa: E402
import georeadiness_analyzer  # noqa: E402
import parse_html  # noqa: E402
import wordpress_api  # noqa: E402

from . import __version__
from .models import (
    ErrorResponse,
    GeoAnalysisRequest,
    GeoAnalysisResponse,
    PageAnalysisRequest,
    PageAnalysisResponse,
    PublishRequest,
    PublishResponse,
    WordPressAuditRequest,
    WordPressAuditResponse,
    WordPressCheckResponse,
)

app = FastAPI(
    title="SEO Blogger Toolkit API",
    description=(
        "REST API exposing the SEO Blogger Toolkit features: page analysis, "
        "GEO readiness, WordPress audit, and publishing."
    ),
    version=__version__,
)

# CORS — allow configurable origins via env var, default to none (locked down).
_cors_origins = os.environ.get("SEO_API_CORS_ORIGINS", "").split(",")
_cors_origins = [o.strip() for o in _cors_origins if o.strip()]
if _cors_origins:
    app.add_middleware(
        CORSMiddleware,
        allow_origins=_cors_origins,
        allow_credentials=True,
        allow_methods=["GET", "POST"],
        allow_headers=["*"],
    )


@app.get("/", tags=["meta"])
def root() -> dict:
    return {
        "name": "SEO Blogger Toolkit API",
        "version": __version__,
        "docs": "/docs",
        "endpoints": [
            "GET  /health",
            "POST /analyze/page",
            "POST /analyze/geo",
            "POST /wordpress/check",
            "POST /wordpress/audit",
            "POST /wordpress/publish",
        ],
    }


@app.get("/health", tags=["meta"])
def health() -> dict:
    return {"status": "ok"}


@app.post(
    "/analyze/page",
    response_model=PageAnalysisResponse,
    responses={400: {"model": ErrorResponse}, 502: {"model": ErrorResponse}},
    tags=["analyze"],
)
def analyze_page(req: PageAnalysisRequest) -> PageAnalysisResponse:
    """Fetch a URL and return parsed SEO data (title, headings, schema, links, etc.)."""
    ua = fetch_page.GOOGLEBOT_USER_AGENT if req.googlebot else None
    fetched = fetch_page.fetch_page(str(req.url), timeout=req.timeout, user_agent=ua)

    if fetched["error"]:
        raise HTTPException(
            status_code=status.HTTP_502_BAD_GATEWAY,
            detail=fetched["error"],
        )
    if not fetched["content"]:
        raise HTTPException(
            status_code=status.HTTP_502_BAD_GATEWAY,
            detail="Empty response body from upstream",
        )

    parsed = parse_html.parse_html(fetched["content"], base_url=fetched["url"])

    return PageAnalysisResponse(
        url=fetched["url"],
        status_code=fetched["status_code"],
        title=parsed["title"],
        meta_description=parsed["meta_description"],
        canonical=parsed["canonical"],
        h1=parsed["h1"],
        h2=parsed["h2"],
        h3=parsed["h3"],
        word_count=parsed["word_count"],
        images_count=len(parsed["images"]),
        internal_links_count=len(parsed["links"]["internal"]),
        external_links_count=len(parsed["links"]["external"]),
        schema_blocks=parsed["schema"],
        open_graph=parsed["open_graph"],
        twitter_card=parsed["twitter_card"],
        hreflang=parsed["hreflang"],
    )


@app.post(
    "/analyze/geo",
    response_model=GeoAnalysisResponse,
    responses={400: {"model": ErrorResponse}, 502: {"model": ErrorResponse}},
    tags=["analyze"],
)
def analyze_geo(req: GeoAnalysisRequest) -> GeoAnalysisResponse:
    """Score a page for GEO readiness (citation-friendliness for AI search engines)."""
    try:
        result = georeadiness_analyzer.analyze_geo_readiness(str(req.url))
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e)) from e
    except Exception as e:  # noqa: BLE001 - upstream library error surface
        raise HTTPException(status_code=502, detail=f"GEO analysis failed: {e}") from e

    if result.get("error"):
        raise HTTPException(status_code=502, detail=result["error"])

    return GeoAnalysisResponse(
        source=result.get("source", str(req.url)),
        geo_readiness_score=result.get("geo_readiness_score", 0),
        score_breakdown=result.get("score_breakdown", {}),
        sections_analyzed=result.get("sections_analyzed", 0),
        sections=result.get("sections", []),
        llms_txt=result.get("llms_txt", {}),
        robots_txt=result.get("robots_txt", {}),
        author_schema=result.get("author_schema", {}),
        recommendations=result.get("recommendations", []),
    )


@app.post(
    "/wordpress/check",
    response_model=WordPressCheckResponse,
    responses={400: {"model": ErrorResponse}},
    tags=["wordpress"],
)
def wordpress_check(req: WordPressAuditRequest) -> WordPressCheckResponse:
    """Detect whether a URL is a WordPress site and report basic metadata."""
    try:
        site_url = wordpress_api.validate_url(str(req.site_url))
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e)) from e

    client = wordpress_api.WordPressClient(
        site_url, username=req.username, password=req.app_password
    )
    info = client.check_wordpress()
    return WordPressCheckResponse(**{
        k: info.get(k)
        for k in (
            "is_wordpress", "api_available", "version", "name",
            "description", "seo_plugin", "error",
        )
    })


@app.post(
    "/wordpress/audit",
    response_model=WordPressAuditResponse,
    responses={400: {"model": ErrorResponse}, 502: {"model": ErrorResponse}},
    tags=["wordpress"],
)
def wordpress_audit(req: WordPressAuditRequest) -> WordPressAuditResponse:
    """Run a full SEO audit of a WordPress site via its REST API."""
    if not req.username or not req.app_password:
        raise HTTPException(
            status_code=400,
            detail="username and app_password are required for full audit",
        )
    try:
        site_url = wordpress_api.validate_url(str(req.site_url))
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e)) from e

    client = wordpress_api.WordPressClient(
        site_url, username=req.username, password=req.app_password
    )
    try:
        audit = client.seo_audit()
    except Exception as e:  # noqa: BLE001
        raise HTTPException(status_code=502, detail=f"Audit failed: {e}") from e

    return WordPressAuditResponse(
        site_url=site_url,
        site_info=audit.get("site_info", {}),
        summary=audit.get("summary", {}),
        posts_count=len(audit.get("posts", [])),
        pages_count=len(audit.get("pages", [])),
        issues=audit.get("issues", {}),
        error=audit.get("error"),
    )


@app.post(
    "/wordpress/publish",
    response_model=PublishResponse,
    responses={400: {"model": ErrorResponse}, 502: {"model": ErrorResponse}},
    tags=["wordpress"],
)
def wordpress_publish(req: PublishRequest) -> PublishResponse:
    """Publish (or draft) a new article to a WordPress site via REST API."""
    try:
        site_url = wordpress_api.validate_url(str(req.site_url))
    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e)) from e

    client = wordpress_api.WordPressClient(
        site_url, username=req.username, password=req.app_password
    )

    category_ids = [client.get_or_create_category(c) for c in req.categories]
    tag_ids = [client.get_or_create_tag(t) for t in req.tags]

    try:
        post = client.create_post(
            title=req.title,
            content=req.content,
            status=req.status,
            categories=category_ids,
            tags=tag_ids,
        )
    except Exception as e:  # noqa: BLE001
        raise HTTPException(status_code=502, detail=f"Publish failed: {e}") from e

    return PublishResponse(
        post_id=post.get("id"),
        link=post.get("link", ""),
        status=post.get("status", req.status),
        title=(post.get("title", {}) or {}).get("rendered", req.title),
    )
