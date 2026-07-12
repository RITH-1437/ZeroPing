# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.1.x   | :white_check_mark: |
| 1.0.x   | :x:                |

Only the latest stable minor release receives security fixes. If you are running
an older version, please upgrade before reporting.

## Reporting a Vulnerability

If you discover a security vulnerability within ZeroPing, please report it
**privately**. Do not open a public GitHub issue for security-sensitive
problems.

You can report a vulnerability in either of the following ways:

- **Email:** send details to **Rin Nairith** at
  [nairithrin143@gmail.com](mailto:nairithrin143@gmail.com).
- **GitHub Security Advisories:** use the
  [Private Vulnerability Reporting](https://github.com/RITH-1437/ZeroPing/security/advisories/new)
  feature on the repository.

Please include:

- A description of the vulnerability and its impact.
- Steps to reproduce, or a proof of concept.
- The affected ZeroPing version(s).

## What to Expect

1. You will receive an acknowledgement within **72 hours**.
2. We will investigate and keep you informed of progress.
3. Once confirmed and fixed, we will coordinate a release and credit you
   (unless you prefer to remain anonymous).
4. A CVE may be requested for significant issues.

## Scope

This policy covers the ZeroPing Framework source in this repository. Third-party
packages, the documentation site, and self-hosted deployments are out of scope.

## Supported Configuration

For production, we recommend:

- Keeping `APP_ENV` set to `production`.
- Enabling CSRF protection and the security middleware.
- Using a strong `APP_KEY` and keeping it secret.
- Keeping PHP and all dependencies up to date.
