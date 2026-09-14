# Versioning & Starter Kit Setup

This document covers how to version the application and how to publish/install it as a reusable starter kit.

---

## Versioning Strategy (Semantic Versioning)

This project uses **Semantic Versioning (SemVer)**. Every release is tagged as `vMAJOR.MINOR.PATCH`:

| Segment  | Rule                                          | Example |
| -------- | --------------------------------------------- | ------- |
| MAJOR    | Incompatible/breaking change                  | `v2.0.0` |
| MINOR    | New feature, backward-compatible              | `v1.1.0` |
| PATCH    | Bug fix, backward-compatible                  | `v1.0.1` |

**Pre-1.0 guidance:** while the starter kit is still stabilizing, treat anything that changes behavior as a MAJOR bump (`0.1.0` → `0.2.0`). A release is only `1.0.0` once the API and admin surface are considered stable.

**Two versions exist in this project — don't confuse them:**

- **Framework version:** `php artisan --version` reports the Laravel framework version (e.g. `Laravel Framework 13.31.0`).
- **Application version:** the git tags on this repository version *your* starter kit (e.g. `v1.0.0`). This is the version Composer uses when consumers run `composer create-project`.

---

## How to Version the Application

1. Decide the new version per the SemVer rules above.
2. Commit all changes for the release.
3. Tag the release and push everything:

```sh
git add -A
git commit -m "feat: version 1.0.0 release"
git push origin main

git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

4. (Optional but recommended) Open **GitHub → Releases → Create a new release** from the tag, and document notable changes.
5. Keep a changelog for consumers. Add and maintain a `CHANGELOG.md` at the repo root, listing each tagged version.

**Useful commands:**

```sh
git describe --tags            # show the current/latest tag
git tag -l                     # list all tags
git show v1.0.0                # inspect a specific release
```

---

## Setup as a Template / Starter Kit

The project is packaged as `abdulsalamamtech/laravelapp` (see `composer.json` `"name"`), has `"type": "project"`, and ships the `post-create-project-cmd` script, which on install automatically:

- copies `.env` from `.env.example`
- generates an application key
- creates the SQLite database file
- runs pending migrations

### Option 1 — Composer create-project via Packagist (public repo)

Publish the repository on [packagist.org](https://packagist.org) (submit the `git@github.com:abdulsalamamtech/laravelapp.git` URL), then consumers install with:

```sh
composer create-project abdulsalamamtech/laravelapp my-app
```

Fresh releases are distributed by tagging a version and pushing the tag; Packagist auto-updates via its webhook.

### Option 2 — Composer create-project via a VCS repository (private repo / no Packagist)

No Packagist submission needed; the GitHub repo URL itself drives the install:

```sh
composer create-project --repository='{"type":"vcs","url":"git@github.com:abdulsalamamtech/laravelapp.git"}' abdulsalamamtech/laravelapp my-app
```

This works for **private repositories** as long as the consumer has GitHub SSH access to them.

### Option 3 — GitHub template repository

1. On GitHub: **Settings → General → Template repository → ON**.
2. Consumers click **"Use this template"** on the repo page (or fork it), then clone their copy.
3. Run the full setup in the new project:

```sh
composer setup
```

`composer setup` runs `composer install`, copies the `.env`, generates the key, migrates, installs npm dependencies, and builds the frontend.

### First-time setup inside any new project

```sh
cd my-app
composer setup                # everything at once
php artisan serve
```

### How Composer resolves the version

`composer create-project` pulls the *default branch* (or the newest tag) of the repository. **Tag every release** (`git tag v1.0.0 && git push origin v1.0.0`) so consumers always receive a stable, versioned copy instead of the latest development state.

---

## Release Checklist

- [ ] Changes committed and pushed (`git push origin main`)
- [ ] Release tag created (`git tag -a vX.Y.Z -m "Release vX.Y.Z"`)
- [ ] Tag pushed (`git push origin vX.Y.Z`)
- [ ] GitHub Release published (optional)
- [ ] `CHANGELOG.md` updated (recommended)
- [ ] Consumers can run `composer create-project abdulsalamamtech/laravelapp my-app`