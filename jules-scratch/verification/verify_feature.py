from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()
    page.goto("http://localhost:8084")
    page.screenshot(path="jules-scratch/verification/verification.png")
    page.get_by_text("Teaching Schedule").click()
    page.screenshot(path="jules-scratch/verification/verification2.png")
    page.get_by_text("Auto-Generate").click()
    page.screenshot(path="jules-scratch/verification/verification3.png")
    browser.close()

with sync_playwright() as playwright:
    run(playwright)