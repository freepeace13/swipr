# Swipr 💘

> A modern, real-time dating-app **proof of concept**, built with Laravel.

Swipr is a full-stack Laravel application featuring domain modelling, an action/contract layer, policies,
real-time broadcasting, and a polished front-end built on Blade + Alpine + Tailwind.

The whole thing is containerised with **Laravel Sail / Docker**, so you can spin it up and play with it
without installing PHP, Composer, or Node on your machine.

---

## ✨ Core features

**Discovery feed (the fun part)**
- Full-screen, **TikTok-style profile feed** — one profile per panel with CSS scroll-snap.
- Navigate however feels natural:
  - 🖱️ **Mouse wheel** to scroll up/down
  - ⌨️ **↑ / ↓ arrow keys** to step between profiles
  - 👆 **Click-and-drag** (and touch) to flick between profiles
- **Infinite scroll** — more profiles load automatically (cursor pagination) as you approach the end.
- A live `current / total` counter so you always know where you are in the stack.

**Smart matching**
- Candidates are filtered by **gender compatibility** (13 gender identities + "interested in" preferences,
  matched reciprocally) and by **mutual age preferences** (with an optional "flexible on age" buffer).
- Profiles can be **ranked by shared interests and interest categories** for more relevant matches.

**Profiles & accounts**
- Rich profiles: bio, birthdate/age, gender, dating preferences (interested in, looking for, age range),
  and weighted **interests** grouped into categories.
- Auto-generated SVG **avatars** when no photo is uploaded (Laravolt Avatar).
- Full auth suite via **Laravel Fortify**: registration, email verification, password reset,
  **two-factor authentication**, and **passkeys**.

**Real-time chat**
- One-to-one **conversations** with send / edit / delete messages.
- **Read receipts** and **live message delivery** over websockets — no page refresh.
- Powered by **Laravel Reverb** broadcasting + **Laravel Echo** on the client.

**Demo data**
- Seeders create a catalogue of **interests** and **500 realistic demo profiles** so the feed and matching
  feel real the moment you log in.

---

## 🧰 Tech stack

| Layer | Tools |
|---|---|
| **Language / framework** | PHP 8.5, Laravel 13 |
| **Front-end** | Blade, Alpine.js, Tailwind CSS v4, Vite |
| **Real-time** | Laravel Reverb (websockets), Laravel Echo, Pusher JS protocol |
| **Auth** | Laravel Fortify (2FA, passkeys, email verification, password reset) |
| **Queue / cache / jobs** | Redis, Laravel Horizon |
| **Database** | MySQL 8.4 |
| **Media & avatars** | Spatie Media Library, Laravolt Avatar |
| **UI icons** | Blade Heroicons |
| **Local dev** | Laravel Sail (Docker), Pint, Pail, PHPUnit |

**Architecture highlights:** single-action invokable controllers, a dedicated `Actions/` + `Contracts/`
layer bound through the container, Eloquent query `#[Scope]`s for the matching logic, policies for
authorization, events for broadcasting, and enums for domain vocabulary (gender, looking-for, interested-in).

---

## 🚀 Getting started

### Prerequisites

You only need **Docker** running:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (macOS/Windows), or Docker Engine +
  Compose plugin (Linux).
- `make` (pre-installed on macOS/Linux; on Windows use WSL2).

That's it — no local PHP, Composer, or Node required. The setup uses a throwaway Docker image to install
dependencies before Sail takes over.

### Quick start (3 commands)

```bash
make setup      # 1. Create .env, configure it for Sail, install PHP dependencies
make up         # 2. Build and start the containers (first run pulls/builds images)
make init       # 3. Run migrations, seed demo data, and build front-end assets
```

Then open **http://localhost** 🎉

> **Tip:** Run `make help` at any time to see every available command.

### Make the chat live (optional, recommended)

Real-time chat needs the websocket server and a queue worker. In a **separate terminal**:

```bash
make realtime   # starts Reverb (websockets) + a queue worker together
```

(Or run them individually with `make reverb` and `make queue`.)

---

## 🧪 Trying it out

The fastest way to see everything working:

1. **Register an account** at http://localhost. You'll be asked for a name, email, password, birthdate,
   and gender.
2. **Skip email verification quickly** — emails are written to the log, but for convenience just run:
   ```bash
   make verify    # marks all accounts as verified
   ```
   (Alternatively, the verification link is printed to the logs — view them with `make logs`.)
3. **Complete your dating preferences** — open **Edit Profile** and set *who you're interested in* and your
   *age range*. This is what powers the matching, so the feed needs it.
4. **Open Discover** and start browsing the 500 seeded profiles — scroll with the wheel, arrow keys, or by
   dragging.
5. **Start a conversation** from a profile to try the real-time chat (make sure `make realtime` is running).

> All seeded demo accounts use the password **`password`** (their emails are randomised). The simplest path
> is to register your own account as above.

---

## 🛠️ Useful commands

| Command | What it does |
|---|---|
| `make setup` | One-time bootstrap: create `.env` + install PHP deps (no host PHP needed) |
| `make up` / `make down` | Start / stop the containers |
| `make init` | Migrate, seed demo data, and build assets |
| `make fresh` | Reset the database and re-seed demo data |
| `make verify` | Mark all accounts as email-verified (handy while testing) |
| `./vendor/bin/sail artisan swipr:fake-users --count=50` | Generate random fake users (with interests) for testing |
| `make dev` | Run the Vite dev server with hot reload |
| `make realtime` | Start Reverb + queue worker (for live chat) |
| `make logs` | Tail the application logs |
| `make shell` | Open a shell inside the app container |
| `make test` | Run the test suite |
| `make build` | Force a clean rebuild of the app image |

---

## 🧯 Troubleshooting

- **Port already in use (80 / 3306 / 6379 / 8080)?** Stop the conflicting service, or set the relevant
  forward port in `.env` (e.g. `APP_PORT`, `FORWARD_DB_PORT`, `FORWARD_REDIS_PORT`, `REVERB_PORT`) and run
  `make up` again.
- **The feed says "No matches yet"?** Make sure you've set *interested in* and an *age range* on your
  profile (step 3 above). Widen the age range or toggle "flexible on age" to see more people.
- **Chat doesn't update live?** Confirm `make realtime` is running and that the page was loaded over
  `http://localhost`.
- **Need a clean slate?** `make fresh` re-runs all migrations and re-seeds the demo data.

---

<p align="center"><em>Built with Laravel — thanks for taking a look! 🙏</em></p>
