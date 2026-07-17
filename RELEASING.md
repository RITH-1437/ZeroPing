# Releasing ZeroPing

This guide explains how to cut a new ZeroPing release and publish it to
[Packagist](https://packagist.org/packages/rith-1437/zeroping).

> **Package name:** `rith-1437/zeroping` (all lowercase, no hyphen). This is the
> canonical Packagist name used by `composer create-project rith-1437/zeroping`.

## Versioning

ZeroPing follows semantic versioning (`MAJOR.MINOR.PATCH`):

- `MAJOR` — breaking changes to the public API.
- `MINOR` — new, backwards-compatible features.
- `PATCH` — backwards-compatible bug fixes.

The canonical version lives in `app/Core/Application/App.php` as
`App::VERSION`. Keep it in sync with the Git tag and the `composer.json`
(no separate version field is required for a `type: project` package).

## Release checklist

1. **Ensure `main` is green.**
   Both the `build` and `lint` workflows must pass, and `php zero test`
   must be green locally.

2. **Update the version.**
   Edit `app/Core/Application/App.php` and set `App::VERSION` to the new
   version (e.g. `1.2.1`).

3. **Update the changelog.**
   Add an entry to `CHANGELOG.md` describing the user-facing changes,
   migrations, and any upgrade notes.

4. **Commit and push.**
   ```bash
   git add -A
   git commit -m "Release vX.Y.Z"
   git push origin main
   ```

5. **Tag the release.**
   ```bash
   git tag -a vX.Y.Z -m "ZeroPing vX.Y.Z"
   git push origin vX.Y.Z
   ```

6. **Create the GitHub Release.**
   Go to **Releases → Draft a new release**, choose the tag `vX.Y.Z`, set the
   title to `ZeroPing vX.Y.Z`, and paste the changelog section as the
   description. Publish it.

   > Packagist subscribes to the GitHub tag webhook, so creating the GitHub
   > Release (or pushing the tag) triggers a Packagist update automatically.
   > If Packagist looks stale, hit **Update** on the Packagist package page.

7. **Verify the dist.**
   ```bash
    composer create-project rith-1437/zeroping /tmp/verify --no-interaction
   cd /tmp/verify && php zero doctor
   ```

## Branch protection (recommended)

On GitHub, protect the `main` branch:

- Require a pull request before merging.
- Require status checks to pass: **build** and **lint**.
- Require branches to be up to date before merging.
- (Optional) Require conversation resolution before merge.

## Labels

Repository labels are managed declaratively in [`.github/labels.yml`](.github/labels.yml)
and synced automatically by the `Sync Labels` workflow whenever that file
changes. To add or rename a label, edit the YAML and merge to `main`.

## Notes

- Never modify the demo application under `arena/` when preparing a release
  of the framework itself.
- The `post-create-project-cmd` script (`scripts/post-create-project.php`)
  regenerates `.env` and `APP_KEY` in every installed project, so secrets are
  never shipped in the dist.
