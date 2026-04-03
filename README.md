# Briefd

**Broadcast Ready, Intelligently Edited, Fresh Daily**

An AI-powered community digest tool that aggregates content from RSS feeds and community platforms, summarises it with Claude AI, and delivers polished email digests to your subscribers.

---

## What is Briefd?

Briefd is a SaaS application for creators, community managers, and publishers who want to keep their audience informed without spending hours curating content manually. Connect your content sources — RSS feeds, subreddits, or other feeds — and Briefd fetches the latest posts on a schedule, summarises them into readable 2-3 sentence briefs using the Claude AI API, and sends a formatted digest email to your subscriber list.

Each workspace in Briefd represents a distinct newsletter or community. A workspace has its own sources, subscriber list, and digest history. Users can manage multiple workspaces, generate digests on demand, preview them before sending, and track delivery status — all from a clean dark-themed dashboard.

Briefd is designed to respect content sources. It reads publicly available content for the purpose of summarisation and attribution, always linking back to the original source. It does not interact with, modify, or re-post content on any platform.

---

## Key Features

- **Authentication** — Email/password registration and Google OAuth sign-in
- **Workspaces** — Isolated environments per newsletter or community, each with their own sources, subscribers, and digests
- **RSS Sources** — Connect any RSS feed; sources are fetched hourly and stored for digest generation
- **AI Digest Generation** — Claude AI summarises each article into 2-3 sentences with a link back to the original; digests are generated as background jobs to avoid timeouts
- **Subscriber Management** — Add and manage email subscribers per workspace
- **Email Delivery** — Send digests to all subscribers via Resend with a clean, branded HTML email template
- **Digest Preview** — Preview the full formatted digest in the dashboard before sending
- **Background Processing** — Digest generation runs in the queue so the UI stays responsive; the page polls and updates automatically when ready
- **Free Tier Limits** — Free plan supports 1 workspace and up to 50 subscribers

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| Frontend components | Livewire 4.2 |
| JS interactivity | Alpine.js (bundled via Livewire) |
| Styling | Tailwind CSS 3 |
| Database | MySQL / MariaDB |
| Queue | Laravel database queue |
| AI summarisation | Anthropic Claude API (`claude-sonnet-4-6`) |
| Email delivery | Resend |
| OAuth | Laravel Socialite (Google) |
| Local dev | Laravel Herd |

---

## How It Works

1. **Connect a source** — Add an RSS feed URL (or subreddit, see below) to your workspace
2. **Sources are fetched** — The `FetchRssSources` job runs hourly, pulling the latest items and storing titles, URLs, and descriptions
3. **Generate a digest** — Click "New Digest" in the dashboard; Briefd creates a draft and dispatches a background job
4. **Claude summarises** — The `GenerateDigest` job sends the 10 most recent items to the Claude API, which writes a 2-3 sentence summary for each with a "Read more" link
5. **Preview and send** — Review the formatted digest in the dashboard, then click "Send Digest" to deliver it to all subscribers via Resend
6. **Subscribers receive it** — Each subscriber gets a clean branded HTML email with all the summaries and links back to original sources

---

## API Integrations

### Anthropic Claude API

Used exclusively for AI summarisation during digest generation. Briefd sends article titles, URLs, and descriptions (from RSS `<description>` fields) to the Claude API and receives back a formatted HTML digest. No content is stored by Anthropic beyond what is necessary to fulfill the API request.

- **Endpoint:** `POST https://api.anthropic.com/v1/messages`
- **Model:** `claude-sonnet-4-6`
- **Purpose:** Summarise article content into 2-3 sentence digests

### Resend

Used to deliver digest emails to subscribers. Briefd uses Laravel's mail system with Resend as the transport driver.

- **Purpose:** Transactional email delivery of digest newsletters
- **Trigger:** Initiated manually by the workspace owner from the digest preview page

### Google OAuth

Used as an optional sign-in method via Laravel Socialite. Briefd requests only the basic profile and email scopes.

- **Scopes:** `openid`, `email`, `profile`
- **Purpose:** Streamlined account creation and sign-in

### Reddit API

See full details in the dedicated section below.

---

## Reddit API Usage

Briefd integrates with the Reddit API to allow users to include subreddit content in their digest newsletters.

### What Briefd does with Reddit

- Fetches the **top or new posts** from user-specified subreddits on a scheduled basis (hourly)
- Reads post **titles, URLs, and selftext content** to include in digest summaries
- Passes this content to the Claude AI API for summarisation
- Includes the summarised content in email digests sent to the workspace's own subscribers, **always linking back to the original Reddit post**

### What Briefd does NOT do

- Does **not** post, submit, or create any content on Reddit
- Does **not** vote (upvote or downvote) on any content
- Does **not** comment on any posts or threads
- Does **not** send private messages or interact with any Reddit users
- Does **not** access any private subreddits or non-public content
- Does **not** scrape or store Reddit content beyond what is needed for digest generation
- Does **not** display Reddit content publicly — digests are sent only to the workspace's own opted-in subscribers

### Access level required

Briefd requires **read-only access** to the Reddit API. Specifically:

- `read` — to fetch posts from public subreddits

No write scopes are requested or used at any point in the application.

### User context

Briefd is used by newsletter creators and community managers who want to curate Reddit content for their own subscriber audiences. For example, a developer advocacy team might pull top posts from a relevant programming subreddit to include in their weekly developer newsletter. All Reddit content included in digests is attributed with the original post URL, directing readers back to Reddit.

---

## Local Development Setup

### Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- MySQL or MariaDB
- [Laravel Herd](https://herd.laravel.com) (recommended) or Laravel Valet

### Installation

```bash
# Clone the repository
git clone https://github.com/your-org/briefd.git
cd briefd

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your .env (see Environment Variables below)

# Run migrations
php artisan migrate

# Build frontend assets
npm run build
```

### Running locally

Open three terminal tabs:

```bash
# Tab 1 — Vite dev server (hot reload)
npm run dev

# Tab 2 — Queue worker (required for digest generation)
php artisan queue:work

# Tab 3 — Scheduler (optional for local testing)
php artisan schedule:work
```

If using Laravel Herd, the site will be available at `http://briefd.test`.

### Manually triggering an RSS fetch

```bash
php artisan tinker
>>> dispatch_sync(new \App\Jobs\FetchRssSources());
```

---

## Environment Variables

Copy `.env.example` to `.env` and fill in the following:

```env
# Application
APP_NAME=Briefd
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://briefd.test

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=briefd
DB_USERNAME=root
DB_PASSWORD=

# Queue — use 'database' for local dev
QUEUE_CONNECTION=database

# Mail / Resend
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=hello@yourdomain.com
MAIL_FROM_NAME=Briefd
RESEND_API_KEY=

# Anthropic Claude API
ANTHROPIC_API_KEY=

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://briefd.test/auth/google/callback

# Reddit OAuth
REDDIT_CLIENT_ID=
REDDIT_CLIENT_SECRET=
REDDIT_REDIRECT_URI=http://briefd.test/auth/reddit/callback
REDDIT_USER_AGENT="web:briefd:v1.0 (by /u/yourusername)"
```

> **Local email testing:** Set `MAIL_MAILER=log` to write emails to `storage/logs/laravel.log` instead of sending via Resend. Resend requires a verified sending domain for live delivery.

---

## License

MIT License. Copyright (c) 2026 Briefd.
