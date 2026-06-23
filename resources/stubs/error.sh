#!/bin/bash

RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m'

echo ""
echo -e "${BOLD}${RED}Failed to generate Laravel application: {{ name }}${NC}"
echo ""
echo "The following errors were found in your request:"
echo ""
{{ errors }}
echo ""
