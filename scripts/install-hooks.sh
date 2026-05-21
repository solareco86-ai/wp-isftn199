#!/bin/bash

# Script to install git hooks

HOOK_DIR=".git/hooks"
PRE_PUSH_HOOK="$HOOK_DIR/pre-push"
SCRIPT_PATH="../../scripts/pre-push"

if [ ! -d "$HOOK_DIR" ]; then
    echo "Error: .git directory not found. Are you in the root of the repository?"
    exit 1
fi

echo "Installing pre-push hook..."

# Create a symbolic link or a wrapper script
# Symbolic links in .git/hooks can be tricky across platforms, 
# so a wrapper script is often more robust.

cat > "$PRE_PUSH_HOOK" <<EOF
#!/bin/bash
# Hook wrapper created by install-hooks.sh
./scripts/pre-push "\$@"
EOF

chmod +x "$PRE_PUSH_HOOK"

echo "Pre-push hook installed successfully!"
