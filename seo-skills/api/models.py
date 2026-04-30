"""Pydantic request/response models for the SEO API."""

from typing import Any, Optional

from pydantic import BaseModel, Field, HttpUrl


class PageAnalysisRequest(BaseModel):
    url: HttpUrl = Field(..., description="URL of the page to analyze")
    timeout: int = Field(30, ge=1, le=120, description="HTTP fetch timeout in seconds")
    googlebot: bool = Field(
        False, description="Use Googlebot UA to detect dynamic rendering"
    )


class PageAnalysisResponse(BaseModel):
    url: str
    status_code: Optional[int]
    title: Optional[str]
    meta_description: Optional[str]
    canonical: Optional[str]
    h1: list[str]
    h2: list[str]
    h3: list[str]
    word_count: int
    images_count: int
    internal_links_count: int
    external_links_count: int
    schema_blocks: list[dict]
    open_graph: dict
    twitter_card: dict
    hreflang: list[dict]


class GeoAnalysisRequest(BaseModel):
    url: HttpUrl = Field(..., description="URL to analyze for GEO readiness")


class GeoAnalysisResponse(BaseModel):
    source: str
    geo_readiness_score: int
    score_breakdown: dict
    sections_analyzed: int
    sections: list[dict]
    llms_txt: dict
    robots_txt: dict
    author_schema: dict
    recommendations: list[str]


class WordPressAuditRequest(BaseModel):
    site_url: HttpUrl = Field(..., description="WordPress site URL")
    username: Optional[str] = Field(
        None, description="WordPress username (optional, for authenticated audit)"
    )
    app_password: Optional[str] = Field(
        None, description="WordPress Application Password"
    )


class WordPressCheckResponse(BaseModel):
    is_wordpress: bool
    api_available: bool
    version: Optional[str] = None
    name: Optional[str] = None
    description: Optional[str] = None
    seo_plugin: Optional[str] = None
    error: Optional[str] = None


class WordPressAuditResponse(BaseModel):
    site_url: str
    site_info: dict
    summary: dict
    posts_count: int
    pages_count: int
    issues: dict
    error: Optional[str] = None


class PublishRequest(BaseModel):
    site_url: HttpUrl
    username: str
    app_password: str
    title: str = Field(..., min_length=1, max_length=300)
    content: str = Field(..., min_length=1)
    status: str = Field("draft", pattern="^(draft|publish|pending|future|private)$")
    categories: list[str] = Field(default_factory=list)
    tags: list[str] = Field(default_factory=list)


class PublishResponse(BaseModel):
    post_id: int
    link: str
    status: str
    title: str


class ErrorResponse(BaseModel):
    error: str
    detail: Optional[Any] = None
