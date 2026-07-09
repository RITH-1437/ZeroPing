# Documentation Search

ZeroPing includes a full-text search engine for your documentation, accessible via the search modal (`Ctrl+K` / `Cmd+K`) or the `/search` API endpoint.

## Search Index

The search index is built from all markdown documents in `docs/`. Each document is parsed into words, stemmed, and stored with positional data for relevance scoring.

### Building the Index

```bash
php zero search:index
```

This scans all `.md` files in the `docs/` directory, builds a term-frequency map, and writes the index to `storage/cache/search.index`.

### Automatic Rebuilding

The index is rebuilt automatically when:

- You run `php zero optimize`
- The `search:index` command is executed
- A cache clear is performed with `php zero optimize:clear`

## Search API

### AJAX Endpoint

```http
GET /search?q=validation
```

Returns JSON:

```json
{
    "query": "validation",
    "count": 3,
    "results": [
        {
            "title": "Validation",
            "url": "/docs/validation",
            "content": "…with <mark>validation</mark> rules…",
            "score": 0.95
        }
    ]
}
```

### Frontend Integration

The search modal in `views/layouts/site.php` is wired to this endpoint with:

- **Debounced input** — 250ms delay before searching
- **Recent searches** — last 5 queries stored in `localStorage`
- **Highlighted results** — matching terms are wrapped in `<mark>` tags
- **Empty state** — friendly message when no results match
- **Error state** — graceful fallback when the server is unreachable

## Fuzzy Matching

Results that don't match exactly are ranked using Levenshtein distance. This means:

- `valdiation` still finds "Validation"
- `instal` finds "Installation"
- `gettin startd` finds "Getting Started"

## Scoring

Results are sorted by:

1. Title match (highest weight)
2. Content term frequency
3. Fuzzy distance (lower = better)
4. Document length (shorter = slightly preferred)

Only results above the relevance threshold (`score > 0.2`) are returned.
