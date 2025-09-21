# Setup Instructions

## Prerequisites

This environment requires Docker and Docker Compose. Since you're using WSL2 on Windows, follow these steps:

### 1. Install Docker Desktop on Windows

1. Download Docker Desktop from: https://www.docker.com/products/docker-desktop
2. Install Docker Desktop with WSL2 backend enabled
3. During installation, ensure "Use WSL 2 instead of Hyper-V" is selected
4. After installation, start Docker Desktop
5. Go to Settings > Resources > WSL Integration
6. Enable integration with your WSL2 distro
7. Apply & Restart

### 2. Verify Docker in WSL2

Open a new WSL2 terminal and run:
```bash
docker --version
docker-compose --version
```

### 3. Start the Environment

Once Docker is running:
```bash
cd /home/r11/dev/wp-pdev
./scripts/start.sh
```

## Alternative: Install Docker in WSL2 Directly

If you prefer not to use Docker Desktop:

```bash
# Update packages
sudo apt update
sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo apt install docker-compose -y

# Start Docker service
sudo service docker start

# Verify installation
docker --version
docker-compose --version
```

## Quick Start After Docker Setup

1. **Start the environment:**
   ```bash
   ./scripts/start.sh
   ```

2. **Access WordPress:**
   - Main site: http://localhost:8080
   - Admin: http://localhost:8080/wp-admin
   - Username: `admin`
   - Password: `admin`
   - MySQL CLI: `./scripts/db-cli.sh`

3. **Your choice-uft plugin** will be automatically linked and activated

## Troubleshooting

### Docker Desktop Not Starting
1. Enable virtualization in BIOS
2. Ensure WSL2 is properly installed: `wsl --install`
3. Update WSL2 kernel: `wsl --update`

### Permission Denied Errors
```bash
sudo usermod -aG docker $USER
newgrp docker
```

### Port Already in Use
Check `.env` file and modify ports if needed:
- WORDPRESS_PORT (default: 8080)
- MYSQL_PORT (default: 3306)
- MAILCATCHER_WEB_PORT (default: 1080)

### WSL2 Network Issues
If localhost doesn't work, try:
- Use the WSL2 IP: `hostname -I`
- Access via Windows host IP
- Restart WSL2: `wsl --shutdown` then reopen