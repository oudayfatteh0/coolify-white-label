# Troubleshooting GitHub Container Registry Authentication

## Common Issues and Solutions

### Issue: "denied: denied" Error

This usually means:
1. Invalid or expired token
2. Token doesn't have correct permissions
3. Wrong username
4. Token format issue

## Step-by-Step Fix

### 1. Create/Verify GitHub Personal Access Token

1. Go to: https://github.com/settings/tokens
2. Click "Generate new token (classic)"
3. Give it a name like "Docker GHCR Push"
4. **Select these scopes:**
   - ✅ `write:packages` (required to push)
   - ✅ `read:packages` (required to pull)
   - ✅ `delete:packages` (optional, for cleanup)
5. Click "Generate token"
6. **Copy the token immediately** (you won't see it again!)

### 2. Login with Correct Format

**Option A: Using echo (Windows Git Bash)**
```bash
# Replace YOUR_ACTUAL_TOKEN with the token you copied
echo YOUR_ACTUAL_TOKEN | docker login ghcr.io -u oudayfatteh0 --password-stdin
```

**Option B: Using PowerShell (if on Windows)**
```powershell
$token = "YOUR_ACTUAL_TOKEN"
echo $token | docker login ghcr.io -u oudayfatteh0 --password-stdin
```

**Option C: Interactive login**
```bash
docker login ghcr.io -u oudayfatteh0
# Then paste your token when prompted for password
```

### 3. Verify Login

```bash
docker login ghcr.io --username oudayfatteh0
```

Or check if you're already logged in:
```bash
cat ~/.docker/config.json
```

### 4. Alternative: Use GitHub CLI (gh)

If you have GitHub CLI installed:

```bash
# Login with GitHub CLI
gh auth login

# Then configure Docker to use gh for authentication
gh auth token | docker login ghcr.io -u oudayfatteh0 --password-stdin
```

## Common Mistakes

1. **Using "YOUR_GITHUB_TOKEN" literally** - Replace with actual token
2. **Token expired** - Generate a new token
3. **Wrong permissions** - Token needs `write:packages`
4. **Username mismatch** - Use your GitHub username exactly
5. **Extra spaces** - Make sure no spaces in token

## Verify Token Permissions

Your token should have:
- ✅ `write:packages` - To push images
- ✅ `read:packages` - To pull images

## Test Authentication

After logging in, try:
```bash
docker pull ghcr.io/oudayfatteh0/coolify-white-label:latest
```

If this works, authentication is successful!

