# APM Demo Template Guide

This folder is a full demo template of the current APM system, copied from:

`/home/apmserver/Desktop/APM-System`

## Location

`/home/apmserver/Desktop/demo-templates/APM-System-Demo-Template`

## What is included

- Same modules, routes, views, controllers, and features as current APM system
- Same UI flows and buttons
- Demo-safe `.env` (no production credentials)
- Setup scripts for fast demo provisioning

## Quick setup

1. Go to demo template:
   - `cd /home/apmserver/Desktop/demo-templates/APM-System-Demo-Template`
2. Install dependencies if needed:
   - `composer install`
   - `npm install`
3. Prepare demo database and app key:
   - `bash scripts/setup-demo.sh`
4. Build frontend:
   - `npm run build`
5. Run demo:
   - `php artisan serve --host=127.0.0.1 --port=8088`

## Notes

- `setup-demo.sh` tries to clone the current source database into `apm_demo_template`.
- If source DB is not available, it falls back to `migrate:fresh --seed`.
- Attachments/files stay under this demo folder’s storage.

## Reset demo data

- `bash scripts/reset-demo.sh`

## Important

- This is intended for sales/demo use.
- Keep production credentials out of this folder.
