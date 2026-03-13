---
description: System Commands for Windows/WSL Environment
---

# System Commands Reference

This document contains validated commands that work in the Windows/WSL environment used for Kromerce development.

## File System Operations

### Remove Files/Directories
```powershell
# Remove directory and all contents
Remove-Item -Recurse -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# Remove single file
Remove-Item -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\file

# Remove directory contents but keep directory
Remove-Item -Recurse -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory\*
```

### Create Directories
```powershell
# Create single directory
New-Item -ItemType Directory -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# Create nested directories
New-Item -ItemType Directory -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\nested\directory
```

### Copy Files/Directories
```powershell
# Copy file
Copy-Item \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\source\file.ext \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\destination\

# Copy directory recursively
Copy-Item -Recurse \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\source\directory \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\destination\
```

### Move Files/Directories
```powershell
# Move file
Move-Item \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\source\file.ext \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\destination\

# Move directory
Move-Item \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\source\directory \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\destination\
```

## File Operations

### Create Files
```powershell
# Create empty file
New-Item -ItemType File -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\file.ext

# Create file with content (use write_to_file tool instead)
# Note: For creating files with content, use the write_to_file tool
```

### Read Files
```powershell
# Read file content
Get-Content \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\file.ext

# Read first N lines
Get-Content -Head 20 \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\file.ext

# Read last N lines
Get-Content -Tail 20 \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\file.ext
```

### Search in Files
```powershell
# ✅ WORKING: Search for text patterns in Vue/JS files recursively
Get-ChildItem -Path "\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\js" -Recurse -Include "*.vue","*.js" | Select-String -Pattern "your-pattern-here"

# Search for text in files (use grep_search tool instead)
# Note: For searching file contents, use the grep_search tool
```

### Search Examples
```powershell
# Search for page.props.auth.user usage
Get-ChildItem -Path "\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\js" -Recurse -Include "*.vue","*.js" | Select-String -Pattern "page\.props\.auth\.user"

# Search for useAuth imports
Get-ChildItem -Path "\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\js" -Recurse -Include "*.vue","*.js" | Select-String -Pattern "useAuth"

# Search for specific component usage
Get-ChildItem -Path "\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\js" -Recurse -Include "*.vue","*.js" | Select-String -Pattern "ComponentName"
```

## Directory Operations

### List Directory Contents
```powershell
# List directory contents
Get-ChildItem \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# List with detailed information
Get-ChildItem \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory | Format-Table

# List recursively
Get-ChildItem -Recurse \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# List only files
Get-ChildItem -File \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# List only directories
Get-ChildItem -Directory \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory
```

### Check if Path Exists
```powershell
# Check if file exists
Test-Path \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\file.ext

# Check if directory exists
Test-Path \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory
```

## Development Commands

### Node.js/NPM Operations
```powershell
# Install dependencies
npm install

# Install specific package
npm install package-name

# Run development server
npm run dev

# Build for production
npm run build

# Run tests
npm test

# Check npm scripts
npm run
```

### Laravel/PHP Operations
```powershell
# Run Laravel artisan commands
php artisan list

# Run migrations
php artisan migrate

# Create new migration
php artisan make:migration create_table_name

# Create new controller
php artisan make:controller ControllerName

# Clear cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Generate application key
php artisan key:generate

# Start development server
php artisan serve
```

### Git Operations
```powershell
# Initialize repository
git init

# Add files to staging
git add .

# Add specific file
git add path/to/file

# Commit changes
git commit -m "Commit message"

# Push to remote
git push origin main

# Pull from remote
git pull origin main

# Check status
git status

# Check current branch
git branch

# Switch branch
git checkout branch-name

# Create new branch
git checkout -b new-branch-name

# Merge branch
git merge branch-name

# View commit history
git log --oneline

# View remote repositories
git remote -v
```

## Text Operations

### Find and Replace in Files
```powershell
# Find text in file (use grep_search tool instead)
# Note: For searching file contents, use the grep_search tool

# Replace text in file (use edit tool instead)
# Note: For editing files, use the edit tool
```

## Network Operations

### Check Network Connectivity
```powershell
# Test connection to host
Test-NetConnection -ComputerName hostname -Port 80

# Ping host
Test-Connection hostname
```

## Process Management

### List Running Processes
```powershell
# List all processes
Get-Process

# Find specific process
Get-Process | Where-Object { $_.ProcessName -like "node" }

# Kill process
Stop-Process -Name "process-name" -Force
```

## Environment Variables

### Get Environment Variables
```powershell
# List all environment variables
Get-ChildItem Env:

# Get specific variable
$env:VARIABLE_NAME

# Set environment variable (temporary)
$env:VARIABLE_NAME = "value"
```

## Common File Paths for Kromerce

### Project Structure
```
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\
├── app\
├── resources\
│   ├── js\
│   │   ├── i18n\
│   │   ├── modules\
│   │   └── layouts\
│   ├── css\
│   └── views\
├── routes\
├── config\
├── database\
├── storage\
└── public\
```

### Key Development Paths
```powershell
# Main application directory
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce

# JavaScript modules
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\js\modules

# Translation files
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\js\i18n\locales

# Laravel controllers
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\app\Http\Controllers

# Laravel routes
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\routes

# Laravel views
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\views

# Laravel config
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\config

# Workflows directory
\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\.windsurf\workflows
```

## Important Notes

### ❌ Commands That Don't Work
- `rm -rf` - Unix command, not available in PowerShell
- `ls` - Unix command, use `Get-ChildItem` instead
- `cp` - Unix command, use `Copy-Item` instead
- `mv` - Unix command, use `Move-Item` instead
- `mkdir` - Unix command, use `New-Item -ItemType Directory` instead
- `touch` - Unix command, use `New-Item -ItemType File` instead
- `cat` - Unix command, use `Get-Content` instead
- `grep` - Unix command, use `Select-String` or grep_search tool instead
- `head` - Unix command, not available in PowerShell
- `Select-String -Recurse` - Invalid parameter, use Get-ChildItem | Select-String instead
- `find` - Unix command, use Get-ChildItem with filters instead

### ✅ Commands That Work
- `Remove-Item` - For deleting files/directories
- `New-Item` - For creating files/directories
- `Copy-Item` - For copying files/directories
- `Move-Item` - For moving files/directories
- `Get-ChildItem` - For listing directory contents
- `Get-Content` - For reading file contents
- `Test-Path` - For checking if paths exist
- `Get-ChildItem -Recurse -Include "*.vue","*.js" | Select-String -Pattern "pattern"` - For searching text in files
- `npm` commands - Work as expected
- `php artisan` commands - Work as expected
- `git` commands - Work as expected

### 🛠️ Tool Preferences
- **For file creation**: Use `write_to_file` tool instead of `New-Item`
- **For file editing**: Use `edit` tool instead of manual text replacement
- **For file reading**: Use `read_file` tool instead of `Get-Content`
- **For searching**: Use `grep_search` tool instead of `Select-String`
- **For directory listing**: Use `list_dir` tool instead of `Get-ChildItem`
- **For finding files**: Use `find_by_name` tool instead of recursive `Get-ChildItem`

## Quick Reference

### Most Common Commands
```powershell
# Remove directory completely
Remove-Item -Recurse -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# Create directory
New-Item -ItemType Directory -Force \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# List directory contents
Get-ChildItem \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\directory

# Check if file exists
Test-Path \\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\path\to\file

# Search for patterns in Vue/JS files
Get-ChildItem -Path "\\wsl.localhost\Ubuntu\home\kronos\Code\kromerce\resources\js" -Recurse -Include "*.vue","*.js" | Select-String -Pattern "your-pattern"

# Install npm dependencies
npm install

# Run Laravel artisan command
php artisan command:name

# Git operations
git add .
git commit -m "Message"
git push origin main
```

This reference should be used whenever you need to execute system commands in the Windows/WSL environment.
