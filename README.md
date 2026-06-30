# Aduanefie Marketplace Monorepo

Unified repository for the Aduanefie marketplace platform.

## Structure

```
├── apps/                    # Frontend applications (Turborepo workspaces)
│   ├── admin/               # Admin Dashboard (React 18 + CRA)
│   ├── website/             # Main Customer Website (Next.js 16)
│   ├── qr-code/             # QR Code App (Vite + React)
│   └── mobile/              # Flutter Mobile App
├── backend/                 # Laravel 12 API Backend
├── packages/                # Shared packages (future)
└── package.json             # Root workspace config
```

## Getting Started

```bash
# Install dependencies
yarn install

# Run all dev servers
yarn dev

# Build all apps
yarn build
```

## Development

See individual app READMEs for specific setup instructions:
- [apps/admin/README.md](apps/admin/README.md)
- [apps/website/README.md](apps/website/README.md)
- [apps/qr-code/README.md](apps/qr-code/README.md)
- [backend/README.md](backend/README.md)
