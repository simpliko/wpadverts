#!/usr/bin/env node

import fs from "node:fs/promises";
import path from "node:path";
import fg from "fast-glob";

const DOMAIN = process.env.PLUGIN_SLUG || "wpadverts";

// This is intentionally pragmatic and checks common WordPress gettext call patterns.
const CALL_PATTERNS = [
  /\b__\s*\(\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\b_e\s*\(\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\besc_html__\s*\(\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\besc_html_e\s*\(\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\besc_attr__\s*\(\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\besc_attr_e\s*\(\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\b_x\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\b_ex\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\besc_html_x\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\besc_attr_x\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\b_n\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*[^,]+,\s*['"]([^'"]+)['"]\s*\)/g,
  /\b_nx\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*[^,]+,\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\b_n_noop\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g,
  /\b_nx_noop\s*\(\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*['"][^'"]*['"]\s*,\s*['"]([^'"]+)['"]\s*\)/g
];

function getLineNumber(content, index) {
  return content.slice(0, index).split("\n").length;
}

async function main() {
  const files = await fg(["**/*.php"], {
    cwd: process.cwd(),
    ignore: ["node_modules/**"],
    followSymbolicLinks: false
  });

  const issues = [];

  for (const file of files) {
    const absolute = path.join(process.cwd(), file);
    const content = await fs.readFile(absolute, "utf8");

    for (const pattern of CALL_PATTERNS) {
      pattern.lastIndex = 0;
      let match;

      while ((match = pattern.exec(content)) !== null) {
        const domain = match[1];

        if (!domain || domain === DOMAIN) {
          continue;
        }

        const line = getLineNumber(content, match.index);
        issues.push(`${file}:${line} expected text-domain \"${DOMAIN}\", found \"${domain}\"`);
      }
    }
  }

  if (issues.length > 0) {
    console.error("Text-domain check failed:\n");
    for (const issue of issues) {
      console.error(`- ${issue}`);
    }
    process.exit(1);
  }

  console.log(`Text-domain check passed (${files.length} PHP files scanned)`);
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
