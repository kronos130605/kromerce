---
description: WSL Commands Reference
---

# WSL Commands Reference

This document contains validated WSL commands that work directly in the Ubuntu WSL environment for Kromerce development.

## Development Environment Setup

### Current Environment Configuration
- **Backend Server**: Nginx + PHP-FPM (always running in WSL)
- **Frontend Development**: `npm run dev` (auto-reloading on file changes)
- **Node.js Version**: v20.19.4 (set as default via NVM)
- **WSL User**: kronos
- **Project Path**: /home/kronos/Code/kromerce

### Important Notes
- **DO NOT start Laravel server** - Nginx is already configured and running
- **DO NOT start npm run dev** - Already running in watch mode
- **Changes are automatically detected** by both backend and frontend
- **Hot reload is enabled** for Vue components and styles

### Development Workflow
1. **Make code changes** → Frontend auto-reloads via Vite
2. **Update PHP files** → Backend auto-restarts via Nginx
3. **Clear caches if needed** → Use artisan commands when necessary

## Node.js Version Management with NVM

### Current Situation
- System Node.js: v14.21.2 (installed before nvm)
- NVM Available: Yes (v0.39.0)
- Target Version: v20.19.4 (already installed via nvm)
- Default Version: v14.21.2 (needs to be changed)

### NVM Commands
```bash
# Load NVM and list available versions
wsl bash -c "source ~/.nvm/nvm.sh && nvm list"

# Use Node.js v20.19.4 for current session
wsl bash -c "source ~/.nvm/nvm.sh && nvm use 20.19.4"

# Set Node.js v20.19.4 as default
wsl bash -c "source ~/.nvm/nvm.sh && nvm alias default 20.19.4"

# Install new Node.js version
wsl bash -c "source ~/.nvm/nvm.sh && nvm install 20.19.4"

# Check current Node.js version after NVM
wsl bash -c "source ~/.nvm/nvm.sh && node --version"
```

### Setting Node.js v20.19.4 as Default

**Execute this command once to set the default version:**
```bash
wsl bash -c "source ~/.nvm/nvm.sh && nvm alias default 20.19.4"
```

**Verify the change:**
```bash
wsl bash -c "source ~/.nvm/nvm.sh && nvm list"
```

### Important: NVM Loading Required

**For all Node.js commands, you must load NVM first:**
```bash
# ❌ This won't work (uses system Node.js v14.21.2)
wsl npm install

# ✅ This works (uses NVM Node.js v20.19.4)
wsl bash -c "source ~/.nvm/nvm.sh && npm install"
```

### Node.js Development Commands (with NVM)

```bash
# Install dependencies with correct Node.js version
wsl bash -c "source ~/.nvm/nvm.sh && npm install"

# Run development server
wsl bash -c "source ~/.nvm/nvm.sh && npm run dev"

# Build for production
wsl bash -c "source ~/.nvm/nvm.sh && npm run build"

# Run tests
wsl bash -c "source ~/.nvm/nvm.sh && npm test"

# Check npm scripts
wsl bash -c "source ~/.nvm/nvm.sh && npm run"

# Update dependencies
wsl bash -c "source ~/.nvm/nvm.sh && npm update"

# Remove node_modules
wsl rm -rf node_modules
wsl bash -c "source ~/.nvm/nvm.sh && npm install"
```

## File System Operations

### Remove Files/Directories
```bash
# Remove directory and all contents
wsl rm -rf /home/kronos/Code/kromerce/path/to/directory

# Remove single file
wsl rm /home/kronos/Code/kromerce/path/to/file

# Remove directory contents but keep directory
wsl rm -rf /home/kronos/Code/kromerce/path/to/directory/*
```

### Create Directories
```bash
# Create single directory
wsl mkdir -p /home/kronos/Code/kromerce/path/to/directory

# Create nested directories
wsl mkdir -p /home/kronos/Code/kromerce/path/to/nested/directory
```

### Copy Files/Directories
```bash
# Copy file
wsl cp /home/kronos/Code/kromerce/source/file.ext /home/kronos/Code/kromerce/destination/

# Copy directory recursively
wsl cp -r /home/kronos/Code/kromerce/source/directory /home/kronos/Code/kromerce/destination/
```

### Move Files/Directories
```bash
# Move file
wsl mv /home/kronos/Code/kromerce/source/file.ext /home/kronos/Code/kromerce/destination/

# Move directory
wsl mv /home/kronos/Code/kromerce/source/directory /home/kronos/Code/kromerce/destination/
```

## File Operations

### Create Files
```bash
# Create empty file
wsl touch /home/kronos/Code/kromerce/path/to/file.ext

# Create file with content
wsl echo "content" > /home/kronos/Code/kromerce/path/to/file.ext

# Append to file
wsl echo "more content" >> /home/kronos/Code/kromerce/path/to/file.ext
```

### Read Files
```bash
# Read file content
wsl cat /home/kronos/Code/kromerce/path/to/file.ext

# Read first N lines
wsl head -n 20 /home/kronos/Code/kromerce/path/to/file.ext

# Read last N lines
wsl tail -n 20 /home/kronos/Code/kromerce/path/to/file.ext

# Read file with line numbers
wsl cat -n /home/kronos/Code/kromerce/path/to/file.ext
```

### Search in Files
```bash
# Search for text in file
wsl grep "search_term" /home/kronos/Code/kromerce/path/to/file.ext

# Search recursively in directory
wsl grep -r "search_term" /home/kronos/Code/kromerce/path/to/directory/

# Search with line numbers
wsl grep -n "search_term" /home/kronos/Code/kromerce/path/to/file.ext

# Case-insensitive search
wsl grep -i "search_term" /home/kronos/Code/kromerce/path/to/file.ext
```

## Directory Operations

### List Directory Contents
```bash
# List directory contents
wsl ls /home/kronos/Code/kromerce/path/to/directory

# List with detailed information
wsl ls -la /home/kronos/Code/kromerce/path/to/directory

# List recursively
wsl ls -R /home/kronos/Code/kromerce/path/to/directory

# List only files
wsl ls -l /home/kronos/Code/kromerce/path/to/directory | grep "^-"

# List only directories
wsl ls -l /home/kronos/Code/kromerce/path/to/directory | grep "^d"

# List with human-readable sizes
wsl ls -lh /home/kronos/Code/kromerce/path/to/directory
```

### Check if Path Exists
```bash
# Check if file exists
wsl test -f /home/kronos/Code/kromerce/path/to/file.ext && echo "File exists" || echo "File does not exist"

# Check if directory exists
wsl test -d /home/kronos/Code/kromerce/path/to/directory && echo "Directory exists" || echo "Directory does not exist"
```

### Find Files
```bash
# Find files by name
wsl find /home/kronos/Code/kromerce -name "filename.ext"

# Find files by pattern
wsl find /home/kronos/Code/kromerce -name "*.js"

# Find directories
wsl find /home/kronos/Code/kromerce -type d -name "directory_name"

# Find files modified in last N days
wsl find /home/kronos/Code/kromerce -mtime -7 -name "*.php"
```

## Development Commands

### Node.js/NPM Operations

**⚠️ IMPORTANT: Always use NVM for Node.js commands to use v20.19.4**

```bash
# Install dependencies with correct Node.js version
wsl bash -c "source ~/.nvm/nvm.sh && npm install"

# Install specific package
wsl bash -c "source ~/.nvm/nvm.sh && npm install package-name"

# Install globally
wsl bash -c "source ~/.nvm/nvm.sh && npm install -g package-name"

# Run development server
wsl bash -c "source ~/.nvm/nvm.sh && npm run dev"

# Build for production
wsl bash -c "source ~/.nvm/nvm.sh && npm run build"

# Run tests
wsl bash -c "source ~/.nvm/nvm.sh && npm test"

# Check npm scripts
wsl bash -c "source ~/.nvm/nvm.sh && npm run"

# Update dependencies
wsl bash -c "source ~/.nvm/nvm.sh && npm update"

# Check Node.js version being used
wsl bash -c "source ~/.nvm/nvm.sh && node --version"

# Check npm version
wsl bash -c "source ~/.nvm/nvm.sh && npm --version"
```

### Quick Node.js Commands Reference

```bash
# Set up Node.js v20.19.4 and run npm command
wsl bash -c "source ~/.nvm/nvm.sh && nvm use 20.19.4 && npm install"

# One-liner for npm commands with correct Node.js version
wsl bash -c "source ~/.nvm/nvm.sh && npm run dev"
```

### Laravel/PHP Operations
```bash
# Run Laravel artisan commands
wsl php artisan list

# Run migrations
wsl php artisan migrate

# Create new migration
wsl php artisan make:migration create_table_name

# Create new controller
wsl php artisan make:controller ControllerName

# Create new model
wsl php artisan make:model ModelName

# Clear cache
wsl php artisan cache:clear

# Clear config cache
wsl php artisan config:clear

# Clear view cache
wsl php artisan view:clear

# Generate application key
wsl php artisan key:generate

# Start development server
wsl php artisan serve

# Create storage link
wsl php artisan storage:link

# Optimize application
wsl php artisan optimize

# Tinker (interactive mode)
wsl php artisan tinker
```

### Git Operations
```bash
# Initialize repository
wsl git init

# Add files to staging
wsl git add .

# Add specific file
wsl git add path/to/file

# Commit changes
wsl git commit -m "Commit message"

# Push to remote
wsl git push origin main

# Pull from remote
wsl git pull origin main

# Check status
wsl git status

# Check current branch
wsl git branch

# Switch branch
wsl git checkout branch-name

# Create new branch
wsl git checkout -b new-branch-name

# Merge branch
wsl git merge branch-name

# View commit history
wsl git log --oneline

# View remote repositories
wsl git remote -v

# Clone repository
wsl git clone repository-url

# Stash changes
wsl git stash

# Apply stashed changes
wsl git stash pop
```

## Text Operations

### Find and Replace in Files
```bash
# Find and replace in file using sed
wsl sed -i 's/old_text/new_text/g' /home/kronos/Code/kromerce/path/to/file.ext

# Find and replace with confirmation
wsl sed -i 's/old_text/new_text/g' /home/kronos/Code/kromerce/path/to/file.ext

# Replace only first occurrence
wsl sed -i '0,/old_text/new_text/' /home/kronos/Code/kromerce/path/to/file.ext
```

### File Content Manipulation
```bash
# Count lines in file
wsl wc -l /home/kronos/Code/kromerce/path/to/file.ext

# Count words in file
wsl wc -w /home/kronos/Code/kromerce/path/to/file.ext

# Sort file contents
wsl sort /home/kronos/Code/kromerce/path/to/file.ext

# Remove duplicate lines
wsl sort -u /home/kronos/Code/kromerce/path/to/file.ext

# Filter lines containing pattern
wsl grep "pattern" /home/kronos/Code/kromerce/path/to/file.ext
```

## System Information

### System Status
```bash
# Current user
wsl whoami

# Current directory
wsl pwd

# System information
wsl uname -a

# Disk usage
wsl df -h

# Memory usage
wsl free -h

# Process list
wsl ps aux

# Check running services
wsl service --status-all
```

### Network Operations
```bash
# Test network connectivity
wsl ping -c 4 google.com

# Check open ports
wsl netstat -tlnp

# Download file
wsl wget url-to-file

# Download file with curl
wsl curl -O url-to-file
```

## Permissions

### File Permissions
```bash
# Change file permissions
wsl chmod 644 /home/kronos/Code/kromerce/path/to/file.ext

# Change directory permissions
wsl chmod 755 /home/kronos/Code/kromerce/path/to/directory

# Change owner
wsl chown kronos:kronos /home/kronos/Code/kromerce/path/to/file.ext

# Make file executable
wsl chmod +x /home/kronos/Code/kromerce/path/to/script.sh
```

## Common File Paths for Kromerce (WSL)

### Project Structure (WSL Paths)
```bash
# Main application directory
/home/kronos/Code/kromerce

# JavaScript modules
/home/kronos/Code/kromerce/resources/js/modules

# Translation files
/home/kronos/Code/kromerce/resources/js/i18n/locales

# Laravel controllers
/home/kronos/Code/kromerce/app/Http/Controllers

# Laravel routes
/home/kronos/Code/kromerce/routes

# Laravel views
/home/kronos/Code/kromerce/resources/views

# Laravel config
/home/kronos/Code/kromerce/config

# Workflows directory
/home/kronos/Code/kromerce/.windsurf/workflows

# Node modules
/home/kronos/Code/kromerce/node_modules

# Storage directory
/home/kronos/Code/kromerce/storage

# Public directory
/home/kronos/Code/kromerce/public
```

## Quick Reference

### Most Common Commands (Development Environment)
```bash
# Remove directory completely
wsl rm -rf /home/kronos/Code/kromerce/path/to/directory

# Create directory
wsl mkdir -p /home/kronos/Code/kromerce/path/to/directory

# List directory contents
wsl ls -la /home/kronos/Code/kromerce/path/to/directory

# Check if file exists
wsl test -f /home/kronos/Code/kromerce/path/to/file

# Clear Laravel caches (when needed)
wsl php artisan cache:clear

# Check Laravel routes
wsl php artisan route:list --name=products

# Git operations
wsl git add .
wsl git commit -m "Message"
wsl git push origin main

# Search for text in files
wsl grep -r "search_term" /home/kronos/Code/kromerce/path/to/directory/

# Find files
wsl find /home/kronos/Code/kromerce -name "*.js"
```

### Node.js Version Management (Essential)
```bash
# Check current Node.js versions
wsl bash -c "source ~/.nvm/nvm.sh && nvm list"

# Use Node.js v20.19.4 for current session
wsl bash -c "source ~/.nvm/nvm.sh && nvm use 20.19.4"

# Verify Node.js version being used
wsl bash -c "source ~/.nvm/nvm.sh && node --version"
```

### ⚠️ Commands NEVER to Run
```bash
# ❌ DON'T RUN - Nginx handles backend
wsl php artisan serve

# ❌ DON'T RUN - npm run dev is already running
wsl bash -c "source ~/.nvm/nvm.sh && npm run dev"
```

### Command Templates (Copy & Paste)
```bash
# Template for Laravel commands
wsl php artisan [command]

# Template for cache clearing
wsl php artisan cache:clear && wsl php artisan config:clear && wsl php artisan view:clear

# Template for route checking (CORRECT SYNTAX)
wsl bash -c "php artisan route:list | grep products"

# Template for route checking with specific name
wsl bash -c "php artisan route:list --name=products"

# Template for log checking
wsl tail -20 storage/logs/laravel.log

# Template for log filtering
wsl bash -c "tail -f storage/logs/laravel.log | grep 'search_term'"
```

## Important Notes

### ✅ Commands That Work in WSL
- All standard Unix commands: `ls`, `cd`, `pwd`, `mkdir`, `rm`, `cp`, `mv`
- Text processing: `cat`, `grep`, `sed`, `awk`, `sort`, `uniq`
- Development tools: `npm`, `php`, `git`, `node`
- System utilities: `ps`, `top`, `df`, `free`, `uname`

### ⚠️ Critical: Node.js Version Management

**ALWAYS use NVM for Node.js commands:**
- System Node.js: v14.21.2 (old, may cause compatibility issues)
- NVM Node.js: v20.19.4 (target version for development)
- **Without NVM**: `wsl npm install` uses v14.21.2
- **With NVM**: `wsl bash -c "source ~/.nvm/nvm.sh && npm install"` uses v20.19.4

**Set default once:**
```bash
wsl bash -c "source ~/.nvm/nvm.sh && nvm alias default 20.19.4"
```

### 🛠️ Tool Preferences
- **For file creation**: Use `write_to_file` tool for better integration
- **For file editing**: Use `edit` tool for precise changes
- **For file reading**: Use `read_file` tool for structured output
- **For searching**: Use `grep_search` tool for advanced search
- **For directory listing**: Use `list_dir` tool for structured output
- **For finding files**: Use `find_by_name` tool for pattern matching

### 🔄 When to Use WSL Commands
- **Quick file operations**: `wsl rm -rf`, `wsl mkdir -p`
- **System information**: `wsl pwd`, `wsl whoami`
- **Laravel commands**: `wsl php artisan migrate`, `wsl php artisan cache:clear`
- **Git operations**: `wsl git add .`, `wsl git commit`, `wsl git push`
- **Text processing**: `wsl grep`, `wsl sed`
- **Batch operations**: Multiple commands in sequence

### 📋 Development Environment Notes
- **Backend**: Nginx + PHP-FPM always running (auto-restarts on changes)
- **Frontend**: `npm run dev` always running in watch mode (auto-reloads)
- **Hot reload**: Enabled for Vue components, styles, and PHP files
- **Manual server startup**: NOT required
- **Cache clearing**: Only when necessary after major changes

### 📋 Command Execution Preference
- **User executes manually**: Copy commands from this workflow
- **No auto-execution**: Commands provided as reference only
- **Environment aware**: Commands adapted for always-running servers
- **NVM required**: Always include `source ~/.nvm/nvm.sh` for Node.js commands

This reference should be used whenever you need to execute Unix-style commands directly in the WSL environment.