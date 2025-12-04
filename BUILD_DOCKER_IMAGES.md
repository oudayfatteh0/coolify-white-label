# Building and Publishing Docker Images

## Problem

The `docker-compose.prod.yml` file uses Docker images from `coollabsio/coolify`, which contain the original Coolify code. Even though your installation script downloads files from your repository, the running containers still use the original Coolify images.

## Solution

You need to build Docker images from your fork and publish them to your own container registry.

## Steps

### 1. Build Docker Images

Build the main Coolify image:

```bash
# Build the production image
docker build \
  --platform linux/amd64 \
  -f docker/production/Dockerfile \
  -t ghcr.io/oudayfatteh0/coolify-white-label:latest \
  -t ghcr.io/oudayfatteh0/coolify-white-label:v4.x \
  .
```

Build the realtime image:

```bash
# Build the realtime image
docker build \
  --platform linux/amd64 \
  -f docker/coolify-realtime/Dockerfile \
  -t ghcr.io/oudayfatteh0/coolify-white-label-realtime:1.0.10 \
  -t ghcr.io/oudayfatteh0/coolify-white-label-realtime:latest \
  .
```

Build the helper image (optional, but recommended):

```bash
# Build the helper image
docker build \
  --platform linux/amd64 \
  -f docker/coolify-helper/Dockerfile \
  -t ghcr.io/oudayfatteh0/coolify-white-label-helper:latest \
  .
```

### 2. Login to GitHub Container Registry

```bash
# Login to GHCR
echo $GITHUB_TOKEN | docker login ghcr.io -u oudayfatteh0 --password-stdin
```

Or if you don't have a token yet:
1. Go to GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Create a token with `write:packages` permission
3. Use it to login

### 3. Push Images to GitHub Container Registry

```bash
# Push main image
docker push ghcr.io/oudayfatteh0/coolify-white-label:latest
docker push ghcr.io/oudayfatteh0/coolify-white-label:v4.x

# Push realtime image
docker push ghcr.io/oudayfatteh0/coolify-white-label-realtime:1.0.10
docker push ghcr.io/oudayfatteh0/coolify-white-label-realtime:latest

# Push helper image (if built)
docker push ghcr.io/oudayfatteh0/coolify-white-label-helper:latest
```

### 4. Make Images Public (Optional)

By default, GitHub Container Registry images are private. To make them public:

1. Go to your GitHub repository
2. Click on "Packages" in the right sidebar
3. Click on each package
4. Go to "Package settings" → "Change visibility" → "Public"

### 5. Update docker-compose.prod.yml

The `docker-compose.prod.yml` file has already been updated to use your images:
- `ghcr.io/oudayfatteh0/coolify-white-label:latest`
- `ghcr.io/oudayfatteh0/coolify-white-label-realtime:1.0.10`

### 6. Update Environment Variable (Optional)

You can also set the `REGISTRY_URL` environment variable in your `.env` file:

```env
REGISTRY_URL=ghcr.io
```

## Automated Build with GitHub Actions

You can create a GitHub Actions workflow to automatically build and push images on every release or push to `v4.x` branch.

Create `.github/workflows/build-docker.yml`:

```yaml
name: Build and Push Docker Images

on:
  push:
    branches:
      - v4.x
  release:
    types: [published]

env:
  REGISTRY: ghcr.io
  IMAGE_NAME: ${{ github.repository }}

jobs:
  build:
    runs-on: ubuntu-latest
    permissions:
      contents: read
      packages: write

    steps:
      - uses: actions/checkout@v4

      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3

      - name: Log in to Container Registry
        uses: docker/login-action@v3
        with:
          registry: ${{ env.REGISTRY }}
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - name: Extract metadata
        id: meta
        uses: docker/metadata-action@v5
        with:
          images: ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}
          tags: |
            type=ref,event=branch
            type=ref,event=pr
            type=semver,pattern={{version}}
            type=semver,pattern={{major}}.{{minor}}
            type=sha

      - name: Build and push Coolify image
        uses: docker/build-push-action@v5
        with:
          context: .
          file: ./docker/production/Dockerfile
          platforms: linux/amd64,linux/arm64
          push: true
          tags: ${{ steps.meta.outputs.tags }}
          labels: ${{ steps.meta.outputs.labels }}

      - name: Build and push Realtime image
        uses: docker/build-push-action@v5
        with:
          context: .
          file: ./docker/coolify-realtime/Dockerfile
          platforms: linux/amd64,linux/arm64
          push: true
          tags: |
            ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}-realtime:latest
            ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}-realtime:1.0.10

      - name: Build and push Helper image
        uses: docker/build-push-action@v5
        with:
          context: .
          file: ./docker/coolify-helper/Dockerfile
          platforms: linux/amd64,linux/arm64
          push: true
          tags: |
            ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}-helper:latest
```

## Testing

After building and pushing images, test the installation:

```bash
# On a fresh VPS
curl -fsSL https://raw.githubusercontent.com/oudayfatteh0/coolify-white-label/refs/heads/v4.x/scripts/install.sh | bash
```

The installation should now pull your custom Docker images with your white-label branding.

## Troubleshooting

### Images are private
- Make sure to set images to public in GitHub Packages settings
- Or use a personal access token with `read:packages` permission

### Build fails
- Check Docker build logs
- Ensure all dependencies are available
- Verify Dockerfile paths are correct

### Images not found
- Verify image names match in `docker-compose.prod.yml`
- Check that images are pushed to the correct registry
- Ensure images are public or authentication is configured

