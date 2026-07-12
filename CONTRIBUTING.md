# Contributing to ZeroPing

Thank you for considering a contribution to the ZeroPing Framework! Every bug
report, fix, feature, and documentation improvement is appreciated.

## Code of Conduct

By participating in this project you agree to abide by our
[Code of Conduct](CODE_OF_CONDUCT.md). Please be respectful and constructive.

## How to Contribute

### Reporting Bugs

Open an issue at <https://github.com/RITH-1437/ZeroPing/issues> and include:

- Your operating system and PHP version (`php -v`).
- The exact steps to reproduce the problem.
- Expected vs. actual behavior, and any relevant error output.

### Suggesting Features

Open an issue tagged `enhancement` describing:

- The problem the feature would solve.
- A concrete, narrow proposal for how it would work.
- Why it fits ZeroPing's [guiding principles](ROADMAP.md#guiding-principles)
  (zero hidden magic, no required runtime dependencies, stable APIs).

### Submitting Changes

1. Fork the repository and clone your fork.
2. Install dependencies: `composer install`.
3. Copy the environment file: `cp .env.example .env`.
4. Create a topic branch:
   ```bash
   git checkout -b fix/short-description
   ```
5. Make your changes, following the coding standards below.
6. Run the test suite and the linter:
   ```bash
   php zero test
   composer validate --strict
   ```
7. Commit with a clear, imperative message (e.g. `Fix session double-start guard`).
8. Push and open a pull request against `main`.

## Coding Standards

- Target **PHP >= 8.1**; use modern, type-safe PHP.
- Follow PSR-12 formatting.
- Add **PHPDoc** to every public method and class.
- Prefer readable, explicit code over clever abstractions.
- No new required runtime dependencies without maintainer approval.
- Keep the framework's "zero magic" philosophy: behavior should be
  discoverable by reading the source.

## Tests & Documentation

- Every bug fix and feature **must** include tests under `tests/`.
- If you add or change functionality, update the docs in `docs/` and, where
  relevant, the CLI help output.
- Run `php zero test` before submitting and ensure it is green.

## Running the Test Suite

```bash
composer install
cp .env.example .env
php zero test
```

## Running the CLI

The `php zero` command exposes the full developer toolchain (migrations,
scaffolding, queues, and more). Run `php zero --help` for the complete list.

## Release Process (Maintainers)

1. Ensure `main` is green in CI.
2. Update `CHANGELOG.md` with the new version's entries.
3. Bump the version, commit, and tag:
   ```bash
   git commit -am "Prepare vX.Y.Z"
   git tag -a vX.Y.Z -m "ZeroPing vX.Y.Z"
   git push && git push --tags
   ```
4. Create the GitHub Release from the tag and publish packages if applicable.
