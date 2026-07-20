#!/bin/bash

RED='\033[0;31m'
BOLD='\033[1m'
CYAN='\033[0;36m'
LIGHT_CYAN='\033[1;36m'
NC='\033[0m'

echo ""
echo -e "${LIGHT_CYAN}  ____ _   _    _    ____ _____ _____ ____  ${NC}"
echo -e "${LIGHT_CYAN} / ___| | | |  / \  |  _ \_   _| ____|  _ \ ${NC}"
echo -e "${LIGHT_CYAN}| |   | |_| | / _ \ | |_) || | |  _| | |_) |${NC}"
echo -e "${LIGHT_CYAN}| |___|  _  |/ ___ \|  _ < | | | |___|  _ < ${NC}"
echo -e "${LIGHT_CYAN} \____|_| |_/_/   \_\_| \_\|_| |_____|_| \_\${NC}"
echo ""
echo -e "${BOLD}${RED}Failed to generate Laravel application: {{ name }}${NC}"
echo ""
echo "The following errors were found in your request:"
echo ""
{{ errors }}
echo ""
