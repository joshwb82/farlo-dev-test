# Farlo Technical Developer Test

Congratulations on reaching the next stage of Farlo's interview process.  
This technical test is designed to give us insight into how you approach real-world WordPress development: environment setup, code structure, data handling, and technical decision-making.

Please read this README carefully before starting. Your submission **must work from scratch** by following the steps below.

---

## Overview

You will be working with a provided WordPress and DDEV setup and a starter theme.

Your task is to configure the local environment, complete the technical requirements outlined in the test brief, and submit your solution in a clean, reproducible state.

[**Full test brief**](https://docs.google.com/document/d/1J7u44W7UJYjCa8UNuMvoT5Vw6k-WYHCmJBlEe8VoQkk/edit?usp=sharing)

---

## Prerequisites

Before you begin, make sure you have the following installed locally:

- Composer  
- Docker  
- DDEV  
- Node.js (LTS recommended)  
- Gulp  
- direnv  

You should be comfortable using the terminal and running CLI-based tooling.

---

## Pre-setup (Important)

Before starting the project, you **must update the project naming** in the following files:

- `/.ddev/config.yaml`  

This ensures consistency across the local environment, theme assets, and tooling.

---

## Initial Environment Setup

Open a terminal in the project root and run:

`ddev config`

When prompted:

- Change the project name from the default to your chosen project name  
- Accept the remaining defaults unless you have a strong reason not to  

This step configures DDEV correctly for your local setup.

---

## Setting Up WordPress Locally

Once the environment is configured, run:

`ddev start`

On first run, this will:

- Start the Docker containers  
- Set up the local database  
- Generate a local `.env` file from `.env.example` using DDEV defaults  
- Install WordPress via Composer  
- Check out the Farlo starter theme  

Wait for this process to complete before proceeding.

---

## Front-end Tooling

The starter theme includes source files under `src/assets/` (SCSS, JavaScript and media).

You are expected to:

- Set up your own front-end build pipeline (Gulp is recommended)  
- Compile assets into a sensible output location (e.g. `dist/`)  
- Add appropriate scripts to `package.json` (build, watch, etc.)  
- Document how to run your build process in your README  

We are assessing **pragmatic decisions and clarity**, not the complexity of the tooling.

---

## Accessing the Site

After `ddev start` completes, DDEV will output the local site URL.

You can also run:

`ddev launch`

to open the site in your browser.

---

## Completing the Test

Once your environment is running:

- Follow the requirements outlined in the Farlo Technical Developer Test brief  
- Implement the importer and front-end templates as described  

Your solution should be:

- Incremental  
- Idempotent  
- Free of PHP warnings and notices  
- Reasonably performant and production-minded  

---

## Submission Requirements

Your final submission must include:

- Your completed code (GitHub repository link or zip file)  
- A README.md that clearly explains:
  - How to set up the project from scratch  
  - How to run the importer  
  - How to build front-end assets  
  - Any assumptions or trade-offs you made  
- Any additional notes you feel are relevant  

Optional but encouraged:

- A short commit history showing your working process  
- A brief “What I’d do next if this were going live” section (for example: performance, caching, monitoring)

---

## Time Guidance

Please aim to spend **no more than 3–4 hours** on this task.

We value clarity, reliability, and good technical judgment over excessive scope.

Good luck — we look forward to reviewing your work.

— Farlo