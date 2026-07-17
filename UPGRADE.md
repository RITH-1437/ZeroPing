# Upgrade Guide

This guide explains how to upgrade between ZeroPing versions. ZeroPing follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html) (`MAJOR.MINOR.PATCH`).

## General Upgrade Steps

1. **Read the changelog** for the target version in [CHANGELOG.md](CHANGELOG.md).
2. **Backup your project** and database before upgrading.
3. **Update dependencies**:

   ```bash
   composer update
   ```

4. **Run migrations** (if any schema changes were made):

   ```bash
   php zero migrate
   ```

5. **Clear caches**:

   ```bash
   php zero optimize:clear
   ```

6. **Verify** with the doctor:

   ```bash
   php zero doctor
   ```

## Upgrading from v1.x to v2.0

### Breaking Changes

- **DI Container**: Service providers must implement `register()` and `boot()` methods.
- **Response System**: Controllers should return `Response` objects or use `$this->view()`.
- **API Resources**: JSON APIs should use `JsonResource` / `JsonCollection` instead of raw arrays.
- **Config structure**: Some config keys were renamed (see [CHANGELOG.md](CHANGELOG.md#v200---2026-07-14)).

### Migration Path

1. Update `composer.json` to require `rith-1437/zeroping: ^2.0`.
2. Run `composer update`.
3. Review changed config files: `php zero publish --group=config`.
4. Update controllers to return `Response` objects where applicable.
5. Run `php zero migrate` and `php zero test`.

## Upgrading to v1.5.0

- New features (relationships, queue, mail, testing) are backwards-compatible.
- Run `php zero migrate` to apply any new schema.
- No breaking changes from v1.0.0.

## Upgrading to v1.3.0

- New error pages (403, 419) are added automatically.
- `php zero publish --group=views` to customize error screens.
- No breaking changes.

## Need Help?

- Check the [CHANGELOG.md](CHANGELOG.md) for details.
- Open a [GitHub Discussion](https://github.com/rith-1437/ZeroPing/discussions) for questions.
- Report issues via [GitHub Issues](https://github.com/rith-1437/ZeroPing/issues).
