# Installation Issue Fix

## Problem

The installation script exits at step 9 because it's trying to pull Docker images that don't exist yet:
- `ghcr.io/oudayfatteh0/coolify-white-label-helper:1.0.12`
- `ghcr.io/oudayfatteh0/coolify-white-label:4.0.0-beta.452`
- `ghcr.io/oudayfatteh0/coolify-white-label-realtime:1.0.10`

## Solutions

### Option 1: Push Your Docker Images First (Recommended)

Before installing, push your Docker images to GitHub Container Registry:

1. **Login to GHCR:**
   ```bash
   echo YOUR_GITHUB_TOKEN | docker login ghcr.io -u oudayfatteh0 --password-stdin
   ```

2. **Push all images:**
   ```bash
   docker push ghcr.io/oudayfatteh0/coolify-white-label:latest
   docker push ghcr.io/oudayfatteh0/coolify-white-label-realtime:1.0.10
   docker push ghcr.io/oudayfatteh0/coolify-white-label-helper:latest
   ```

3. **Make images public** in GitHub Packages settings

4. **Then run installation again**

### Option 2: Check the Log File

The upgrade script logs errors to a file. Check it:

```bash
# Find the latest log file
ls -lt /data/coolify/source/upgrade-*.log | head -1

# View the log
cat /data/coolify/source/upgrade-*.log | tail -50
```

This will show you the exact error.

### Option 3: Temporarily Use Original Images

If you need to install now and push images later, you can temporarily modify `docker-compose.prod.yml` to use original Coolify images:

```yaml
# Temporarily use original images
image: "${REGISTRY_URL:-ghcr.io}/coollabsio/coolify:${LATEST_IMAGE:-latest}"
```

Then after pushing your images, change it back.

### Option 4: Manual Installation Steps

If the script fails, you can manually complete the installation:

```bash
# 1. Check what failed
cat /data/coolify/source/upgrade-*.log

# 2. Pull images manually (if they exist)
docker pull ghcr.io/oudayfatteh0/coolify-white-label:latest
docker pull ghcr.io/oudayfatteh0/coolify-white-label-realtime:1.0.10
docker pull ghcr.io/oudayfatteh0/coolify-white-label-helper:latest

# 3. Start containers manually
cd /data/coolify/source
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# 4. Wait for migrations
sleep 20

# 5. Check status
docker compose ps
```

## Next Steps

1. **Push your Docker images** (see PUSH_IMAGES.md)
2. **Make them public** in GitHub Packages
3. **Re-run the installation script** or manually start containers

The updated upgrade.sh script now includes better error handling and will show you what went wrong.

