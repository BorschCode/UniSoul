# Security Policy - Version Table Examples

This document provides various examples for the "Supported Versions" table in SECURITY.md. Choose the one that best fits your project's versioning strategy.

## Example 1: Simple Active Development (Current UniSoul Default)

**Use when:** Your project is actively developed with clear major/minor versions and LTS support.

```markdown
| Version | Supported          | Notes                           |
| ------- | ------------------ | ------------------------------- |
| 2.x.x   | :white_check_mark: | Latest stable release           |
| 1.5.x   | :white_check_mark: | LTS - Security fixes only       |
| 1.4.x   | :x:                | End of life                     |
| 1.3.x   | :x:                | End of life                     |
| 1.2.x   | :x:                | End of life                     |
| 1.1.x   | :x:                | End of life                     |
| 1.0.x   | :x:                | End of life                     |
| < 1.0   | :x:                | Beta/Alpha - Not supported      |
```

---

## Example 2: Rolling Release (Latest Only)

**Use when:** You only support the latest version and encourage frequent updates.

```markdown
| Version | Supported          | Notes                           |
| ------- | ------------------ | ------------------------------- |
| latest  | :white_check_mark: | Current release                 |
| < 2.0   | :x:                | Please upgrade to latest        |
```

---

## Example 3: Multiple Active Branches

**Use when:** You maintain multiple major versions simultaneously.

```markdown
| Version | Supported          | Notes                           |
| ------- | ------------------ | ------------------------------- |
| 5.1.x   | :white_check_mark: | Latest stable                   |
| 5.0.x   | :white_check_mark: | Maintained until Jun 2025       |
| 4.2.x   | :white_check_mark: | LTS - Security fixes only       |
| 4.1.x   | :x:                | End of life                     |
| 4.0.x   | :x:                | End of life                     |
| 3.x.x   | :x:                | End of life                     |
| < 3.0   | :x:                | No longer supported             |
```

---

## Example 4: With Security Support Timeline

**Use when:** You want to be explicit about support end dates.

```markdown
| Version | Supported          | Security Support Until |
| ------- | ------------------ | ---------------------- |
| 3.x     | :white_check_mark: | Dec 2025               |
| 2.9.x   | :white_check_mark: | Jun 2025               |
| 2.8.x   | :warning:          | Mar 2025               |
| 2.7.x   | :x:                | Ended Dec 2024         |
| < 2.7   | :x:                | Not supported          |

**Legend:**
- :white_check_mark: = Actively supported
- :warning: = Limited support (ending soon)
- :x: = No longer supported
```

---

## Example 5: Minimal (Early Stage Project)

**Use when:** Your project is new or in early stages.

```markdown
| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |
| 0.x     | :x:                |
```

---

## Example 6: With Severity Levels

**Use when:** You want to clarify what types of fixes are provided.

```markdown
| Version | Critical | High | Medium | Low | Notes              |
| ------- | -------- | ---- | ------ | --- | ------------------ |
| 4.2.x   | ✅       | ✅   | ✅     | ✅  | Full support       |
| 4.1.x   | ✅       | ✅   | ✅     | ❌  | Maintenance mode   |
| 4.0.x   | ✅       | ✅   | ❌     | ❌  | Security only      |
| 3.x     | ✅       | ❌   | ❌     | ❌  | Critical fixes only|
| < 3.0   | ❌       | ❌   | ❌     | ❌  | End of life        |
```

---

## Example 7: Laravel-Style Versioning

**Use when:** Following Laravel's versioning pattern with LTS releases.

```markdown
| Version | PHP      | Release Date | Bug Fixes Until | Security Fixes Until |
| ------- | -------- | ------------ | --------------- | -------------------- |
| 11.x    | 8.2-8.4  | Mar 2024     | Sep 2025        | Mar 2026             |
| 10.x    | 8.1-8.3  | Feb 2023     | Aug 2024        | Feb 2025             |
| 9.x LTS | 8.0-8.2  | Feb 2022     | Feb 2024        | Feb 2025             |
| 8.x     | 7.3-8.1  | Sep 2020     | Jul 2022        | Jan 2023             |
| < 8.0   | Various  | -            | :x:             | :x:                  |
```

---

## Example 8: With Dependency Requirements

**Use when:** Different versions support different dependencies/platforms.

```markdown
| Version | Status | PHP   | Laravel | PostgreSQL | MySQL   | Redis |
| ------- | ------ | ----- | ------- | ---------- | ------- | ----- |
| 3.x     | ✅     | ≥8.2  | ≥11.0   | ≥15        | ≥8.0    | ≥7.0  |
| 2.x     | ✅     | ≥8.1  | ≥10.0   | ≥14        | ≥5.7    | ≥6.2  |
| 1.x     | ⚠️     | ≥8.0  | ≥9.0    | ≥13        | ≥5.7    | ≥6.0  |
| < 1.0   | ❌     | -     | -       | -          | -       | -     |

**Legend:** ✅ Full support | ⚠️ Security only | ❌ End of life
```

---

## Example 9: Docker Tag Based

**Use when:** Your project is primarily distributed via Docker.

```markdown
| Docker Tag        | Version | Supported          | Base Image    |
| ----------------- | ------- | ------------------ | ------------- |
| `latest`          | 2.x     | :white_check_mark: | Ubuntu 24.04  |
| `2.x`, `2.1`      | 2.1.x   | :white_check_mark: | Ubuntu 24.04  |
| `2.0`             | 2.0.x   | :white_check_mark: | Ubuntu 24.04  |
| `1.x`, `lts`      | 1.5.x   | :white_check_mark: | Ubuntu 22.04  |
| `1.4`             | 1.4.x   | :x:                | Ubuntu 22.04  |
| `1.0` - `1.3`     | 1.0-1.3 | :x:                | Ubuntu 20.04  |
```

---

## Example 10: With Update Path

**Use when:** You want to guide users on upgrade paths.

```markdown
| Version | Supported          | Upgrade Path        | Notes                |
| ------- | ------------------ | ------------------- | -------------------- |
| 3.x     | :white_check_mark: | N/A (latest)        | PHP 8.2+ required    |
| 2.x     | :white_check_mark: | Upgrade to 3.x      | LTS until 2026       |
| 1.9.x   | :warning:          | Upgrade to 2.x      | Support ends Q1 2025 |
| 1.8.x   | :x:                | Upgrade to 2.x      | No longer supported  |
| < 1.8   | :x:                | Upgrade to 2.x      | No longer supported  |

**Legend:**
- :white_check_mark: = Full support
- :warning: = Limited/deprecation period
- :x: = End of life
```

---

## Example 11: Comprehensive (Enterprise)

**Use when:** Running an enterprise project with detailed support SLAs.

```markdown
| Version | Release    | Active Support | Security Support | Status             | PHP   |
| ------- | ---------- | -------------- | ---------------- | ------------------ | ----- |
| 5.1.x   | 2024-11-01 | 2025-11-01     | 2026-11-01       | :white_check_mark: | ≥8.2  |
| 5.0.x   | 2024-06-01 | 2025-06-01     | 2026-06-01       | :white_check_mark: | ≥8.2  |
| 4.5.x   | 2023-12-01 | 2024-12-01     | 2025-12-01       | :warning:          | ≥8.1  |
| 4.0.x   | 2023-01-01 | 2024-01-01     | 2024-07-01       | :x:                | ≥8.1  |
| 3.x     | 2022-01-01 | 2023-01-01     | 2023-07-01       | :x:                | ≥8.0  |
| < 3.0   | -          | -              | -                | :x:                | -     |

**Support Levels:**
- **Active Support**: All bug fixes, security patches, and feature updates
- **Security Support**: Critical security patches only
- **End of Life**: No updates provided

**Status Legend:**
- :white_check_mark: = Full support (Active Support phase)
- :warning: = Security fixes only (Security Support phase)
- :x: = No support (End of Life)
```

---

## Example 12: Beta/RC Support

**Use when:** You actively support beta/release candidate versions.

```markdown
| Version      | Type    | Supported          | Notes                        |
| ------------ | ------- | ------------------ | ---------------------------- |
| 2.1.0        | Stable  | :white_check_mark: | Latest stable release        |
| 2.1.0-rc2    | RC      | :white_check_mark: | Release candidate testing    |
| 2.1.0-beta   | Beta    | :warning:          | Testing only                 |
| 2.0.x        | Stable  | :white_check_mark: | Previous stable              |
| 1.5.x        | LTS     | :white_check_mark: | Long-term support            |
| < 1.5        | Legacy  | :x:                | No longer supported          |

**Legend:**
- :white_check_mark: = Supported
- :warning: = Testing/development use only
- :x: = Not supported
```

---

## Choosing the Right Format

**Consider these factors:**

1. **Project Maturity**
   - New projects: Use Example 2 or 5
   - Mature projects: Use Example 1, 3, or 11

2. **Release Frequency**
   - Frequent releases: Example 2 (rolling)
   - Regular schedule: Example 7 (Laravel-style)
   - Stable/LTS: Example 1 or 3

3. **Audience**
   - Developers: Examples 8, 9 (dependency info)
   - Enterprise: Example 11 (detailed SLAs)
   - General users: Example 1, 4 (simple)

4. **Support Resources**
   - Limited resources: Example 2 (latest only)
   - Good resources: Example 3 (multiple versions)
   - Enterprise team: Example 11 (comprehensive)

5. **Distribution Method**
   - Docker-based: Example 9
   - Package manager: Example 7
   - Direct download: Example 1 or 4

---

## Quick Reference Icons

Commonly used emoji/icons in security policies:

- ✅ :white_check_mark: = Supported
- ❌ :x: = Not supported
- ⚠️ :warning: = Limited/deprecating
- 🔒 :lock: = Security fixes only
- 🚀 :rocket: = Latest/recommended
- 📦 :package: = LTS version
- ⏰ :alarm_clock: = Ending soon
- 💀 :skull: = End of life
- 🧪 :test_tube: = Beta/experimental
- 🏗️ :building_construction: = Under development

---

**To Update:** Simply copy the markdown table from your preferred example into SECURITY.md and adjust the version numbers to match your actual releases.
