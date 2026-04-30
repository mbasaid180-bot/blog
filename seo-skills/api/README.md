# SEO Blogger Toolkit - REST API

Standalone FastAPI server exposing the SEO toolkit features (page analysis,
GEO readiness, WordPress audit, publishing) as HTTP endpoints. Lets you
integrate the toolkit into any external system without going through the
Claude Code CLI.

## Install

```bash
pip install -r requirements.txt -r api/requirements.txt
```

## Run

From the `seo-skills/` directory:

```bash
uvicorn api.main:app --reload --port 8000
```

Interactive docs: http://localhost:8000/docs

## Endpoints

| Method | Path                  | Purpose                                          |
|--------|-----------------------|--------------------------------------------------|
| GET    | `/`                   | API metadata                                     |
| GET    | `/health`             | Liveness check                                   |
| POST   | `/analyze/page`       | Fetch URL + extract SEO data (titles, headings, schema, links) |
| POST   | `/analyze/geo`        | GEO readiness score for AI search engines        |
| POST   | `/wordpress/check`    | Detect WordPress + report version / SEO plugin   |
| POST   | `/wordpress/audit`    | Full SEO audit of a WP site (auth required)      |
| POST   | `/wordpress/publish`  | Publish or draft an article via WP REST API      |

## Examples

### Analyze a page

```bash
curl -X POST http://localhost:8000/analyze/page \
  -H "Content-Type: application/json" \
  -d '{"url": "https://example.com/article", "timeout": 30}'
```

### GEO readiness

```bash
curl -X POST http://localhost:8000/analyze/geo \
  -H "Content-Type: application/json" \
  -d '{"url": "https://example.com/article"}'
```

### WordPress detection (no auth needed)

```bash
curl -X POST http://localhost:8000/wordpress/check \
  -H "Content-Type: application/json" \
  -d '{"site_url": "https://monblog.com"}'
```

### Full WordPress audit

```bash
curl -X POST http://localhost:8000/wordpress/audit \
  -H "Content-Type: application/json" \
  -d '{
    "site_url": "https://monblog.com",
    "username": "admin",
    "app_password": "xxxx xxxx xxxx xxxx"
  }'
```

### Publish a draft

```bash
curl -X POST http://localhost:8000/wordpress/publish \
  -H "Content-Type: application/json" \
  -d '{
    "site_url": "https://monblog.com",
    "username": "admin",
    "app_password": "xxxx xxxx xxxx xxxx",
    "title": "Mon nouvel article",
    "content": "<p>Contenu HTML...</p>",
    "status": "draft",
    "categories": ["SEO"],
    "tags": ["wordpress", "api"]
  }'
```

## Configuration

| Env var                | Default | Description                                              |
|------------------------|---------|----------------------------------------------------------|
| `SEO_API_CORS_ORIGINS` | (empty) | Comma-separated list of allowed origins for CORS.        |

By default CORS is disabled (no `Access-Control-Allow-Origin` header).
Set `SEO_API_CORS_ORIGINS=https://yourapp.com,https://other.com` to enable.

## Security

- All URL inputs go through `validate_url()` for SSRF protection (blocks
  private/loopback/reserved IPs).
- WordPress credentials are passed in the request body — use HTTPS in
  production. Do not log request bodies on the server side.
- The API does not persist credentials. Each request is stateless.

## Notes

- The audit endpoint can take 10-60 seconds on large sites because it iterates
  through all posts and pages via the WordPress REST API. Configure your
  reverse proxy timeout accordingly.
- Run behind a reverse proxy (nginx, Caddy) with TLS in production.
