# Push Docker Images to GitHub Container Registry

## Prerequisites

1. **GitHub Personal Access Token** with `write:packages` permission:
   - Go to: https://github.com/settings/tokens
   - Click "Generate new token (classic)"
   - Select `write:packages` scope
   - Copy the token

## Steps

### 1. Login to GitHub Container Registry

```bash
# Replace YOUR_GITHUB_TOKEN with your actual token
echo YOUR_GITHUB_TOKEN | docker login ghcr.io -u oudayfatteh0 --password-stdin
```

### 2. Push All Images

```bash
# Push main Coolify image
docker push ghcr.io/oudayfatteh0/coolify-white-label:latest

# Push realtime image
docker push ghcr.io/oudayfatteh0/coolify-white-label-realtime:1.0.10

# Push helper image
docker push ghcr.io/oudayfatteh0/coolify-white-label-helper:latest
```

### 3. Make Images Public

After pushing, make the images public:

1. Go to your GitHub repository: https://github.com/oudayfatteh0/coolify-white-label
2. Click on "Packages" in the right sidebar (or go to https://github.com/oudayfatteh0?tab=packages)
3. You should see three packages:
   - `coolify-white-label`
   - `coolify-white-label-realtime`
   - `coolify-white-label-helper`
4. Click on each package
5. Go to "Package settings" → "Change visibility" → Select "Public" → "I understand, change visibility"

### 4. Verify Installation

After making images public, test a fresh installation:

```bash
# On a fresh VPS
curl -fsSL https://raw.githubusercontent.com/oudayfatteh0/coolify-white-label/refs/heads/v4.x/scripts/install.sh | bash
```

The installation should now pull your white-label Docker images!

## Troubleshooting

### Authentication Failed
- Make sure your GitHub token has `write:packages` permission
- Verify you're using the correct username (`oudayfatteh0`)

### Image Not Found After Push
- Wait a few minutes for GitHub to process the images
- Check that images are set to public
- Verify the image names match exactly in `docker-compose.prod.yml`

### Build Failed
- Check Docker build logs for specific errors
- Ensure you have enough disk space
- Try building with `--no-cache` flag if needed

