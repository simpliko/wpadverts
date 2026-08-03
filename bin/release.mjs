#!/usr/bin/env node

import fs from "node:fs/promises";
import path from "node:path";
import { spawn } from "node:child_process";
import fg from "fast-glob";
import fse from "fs-extra";
import wpPot from "wp-pot";

const PLUGIN_SLUG = process.env.PLUGIN_SLUG || "wpadverts";
const SVN_PATH = process.env.SVN_PATH || "/home/greg/svn/wpadverts";
const RELEASE_PATH = process.env.RELEASE_PATH || "/home/greg/Release/wpadverts";

const DEPLOY_EXCLUDES = [
  "node_modules/**",
  "Gruntfile.js",
  "package.json",
  ".gitignore",
  "nbproject/**",
  "tests/**",
  "bin/**",
  ".phpcs.xml.dist",
  ".travis.yml",
  "phpunit.xml.dist",
  "blocks.webpack.js",
  "package-lock.json",
  "postcss.config.js",
  "tailwind.config.js",
  "blocks/*/src/*.js",
  "blocks/*/build/index.js.map",
  "assets/jsx/**",
  "assets/css/tailwind.css"
];

const BUILD_EXCLUDES = [
  "node_modules/**",
  "Gruntfile.js",
  "package.json",
  ".gitignore",
  "nbproject/**",
  "tests/**",
  "bin/**",
  ".phpcs.xml.dist",
  ".travis.yml",
  "phpunit.xml.dist",
  "blocks.webpack.js",
  "package-lock.json",
  "postcss.config.js",
  "tailwind.config.js",
  "blocks/*/src/*.js"
];

function usage() {
  console.error("Usage: node bin/release.mjs <copy|clean|pot|zip> <deploy|build>");
  process.exit(1);
}

function resolveTarget(mode) {
  if (mode === "deploy") {
    return path.join(SVN_PATH, "trunk");
  }

  if (mode === "build") {
    return RELEASE_PATH;
  }

  usage();
}

async function copyTo(targetDir, mode) {
  if (mode === "build") {
    const parentDir = path.dirname(targetDir);
    const zipPath = path.join(parentDir, `${PLUGIN_SLUG}.zip`);

    await fse.remove(targetDir);
    await fse.remove(zipPath);
  }

  const ignore = mode === "deploy" ? DEPLOY_EXCLUDES : BUILD_EXCLUDES;
  const files = await fg(["**/*"], {
    cwd: process.cwd(),
    onlyFiles: true,
    ignore,
    followSymbolicLinks: false
  });

  await Promise.all(
    files.map(async (file) => {
      const source = path.join(process.cwd(), file);
      const destination = path.join(targetDir, file);
      await fse.ensureDir(path.dirname(destination));
      await fse.copyFile(source, destination);
    })
  );

  console.log(`Copied ${files.length} files to ${targetDir}`);
}

async function cleanBuild(targetDir) {
  await fse.remove(targetDir);
  console.log(`Removed ${targetDir}`);
}

function runBestzip(zipPath, sourceDir, cwd = process.cwd()) {
  return new Promise((resolve, reject) => {
    const child = spawn(
      process.platform === "win32" ? "npx.cmd" : "npx",
      ["bestzip", zipPath, sourceDir],
      { stdio: "inherit", cwd }
    );

    child.on("exit", (code) => {
      if (code === 0) {
        resolve();
      } else {
        reject(new Error(`bestzip exited with code ${code}`));
      }
    });

    child.on("error", reject);
  });
}

async function buildZip(targetDir) {
  const parentDir = path.dirname(targetDir);
  const zipPath = path.join(parentDir, `${PLUGIN_SLUG}.zip`);
  const sourceDir = path.basename(targetDir);

  await fs.mkdir(parentDir, { recursive: true });
  await runBestzip(zipPath, sourceDir, parentDir);
}

async function generatePot(targetDir) {
  const potDir = path.join(targetDir, "languages");
  await fs.mkdir(potDir, { recursive: true });

  await wpPot({
    src: "**/*.php",
    destFile: path.join(potDir, `${PLUGIN_SLUG}.pot`),
    domain: PLUGIN_SLUG,
    package: PLUGIN_SLUG,
    relativeTo: targetDir,
    metadataFile: `${PLUGIN_SLUG}.php`,
    writeFile: true,
    globOpts: {
      cwd: targetDir,
      ignore: ["node_modules/**"]
    }
  });

  console.log(`Generated POT: ${path.join(potDir, `${PLUGIN_SLUG}.pot`)}`);
}

async function main() {
  const [action, mode] = process.argv.slice(2);

  if (!action || !mode) {
    usage();
  }

  const targetDir = resolveTarget(mode);

  if (action === "copy") {
    await copyTo(targetDir, mode);
    return;
  }

  if (action === "clean") {
    if (mode !== "build") {
      throw new Error("Clean is available only for build mode");
    }
    await cleanBuild(targetDir);
    return;
  }

  if (action === "pot") {
    await generatePot(targetDir);
    return;
  }

  if (action === "zip") {
    if (mode !== "build") {
      throw new Error("Zip is available only for build mode");
    }
    await buildZip(targetDir);
    return;
  }

  usage();
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
