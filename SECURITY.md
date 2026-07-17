# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 2.0.x | Yes |
| 1.x | No |

Only the latest stable minor release receives security fixes. If you are running an older version, please upgrade before reporting.

## Reporting a Vulnerability

If you discover a security vulnerability within ZeroPing, please report it **privately**. Do not open a public GitHub issue for security-sensitive problems.

### How to Report

- **Email**: send details to **Rin Nairith** at [nairithrin143@gmail.com](mailto:nairithrin143@gmail.com).
- **GitHub Security Advisories**: use the [Private Vulnerability Reporting](https://github.com/RITH-1437/ZeroPing/security/advisories/new) feature on the repository.

### What to Include

- A description of the vulnerability and its impact.
- Steps to reproduce, or a proof of concept.
- The affected ZeroPing version(s).
- Any suggested fix (if applicable).

## What to Expect

1. **Acknowledgement** within **72 hours**.
2. We will investigate and keep you informed of progress.
3. Once confirmed and fixed, we will coordinate a release and credit you (unless you prefer to remain anonymous).
4. A CVE may be requested for significant issues.

## Severity Classification

| Severity | Description | Response Time |
|----------|-------------|---------------|
| Critical | Remote code execution, SQL injection, authentication bypass | Immediate |
| High | Privilege escalation, CSRF bypass, data exposure | Within 48 hours |
| Medium | XSS, information disclosure, denial of service | Within 1 week |
| Low | Minor information leaks, non-sensitive data exposure | Within 2 weeks |

## Scope

This policy covers the ZeroPing Framework source in this repository. Third-party packages, the documentation site, and self-hosted deployments are out of scope.

## Supported Configuration

For production, we recommend:

- Keeping `APP_ENV` set to `production`.
- Enabling CSRF protection and the security middleware.
- Using a strong `APP_KEY` and keeping it secret.
- Keeping PHP and all dependencies up to date.

## Disclosure Policy

We follow coordinated disclosure:

1. The reporter gives us reasonable time to address the issue before public disclosure.
2. We will acknowledge the report and work on a fix.
3. We will release a patch and publish a security advisory.
4. The reporter will be credited (unless they prefer anonymity).
