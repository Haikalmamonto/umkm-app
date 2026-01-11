#!/bin/bash
npm install
npm run build
# Copy build files to dist directory for Vercel
cp -r public/build dist 2>/dev/null || mkdir dist && cp -r public/build/* dist/ 2>/dev/null || echo "No build files to copy"