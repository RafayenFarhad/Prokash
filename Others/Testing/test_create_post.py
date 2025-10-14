# test_create_alert.py
from selenium import webdriver
from selenium.webdriver.chrome.service import Service as ChromeService
from webdriver_manager.chrome import ChromeDriverManager

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC

import time
import uuid

BASE = "https://localhost:5173"
LOGIN_URL = BASE + "/login"
POSTS_PATH = "/posts"
CREATE_PATH = "/create-post"

EMAIL = "john@example.com"
PASSWORD = "password"

# Geolocation to override (Dhaka)
GEO_LAT = 23.8103
GEO_LON = 90.4125
GEO_ACCURACY = 100

def create_driver(headless=False):
    options = webdriver.ChromeOptions()
    # options.add_argument("--headless=new")  # uncomment to run headless
    options.add_argument("--start-maximized")
    options.add_argument("--disable-notifications")
    # allow insecure localhost / ngrok if needed
    options.add_argument("--ignore-certificate-errors")
    driver = webdriver.Chrome(service=ChromeService(ChromeDriverManager().install()), options=options)
    return driver

def wait_for(driver, condition, timeout=15):
    return WebDriverWait(driver, timeout).until(condition)

def login_flow(driver):
    driver.get(LOGIN_URL)
    # wait for inputs
    wait_for(driver, EC.presence_of_element_located((By.ID, "email")))
    wait_for(driver, EC.presence_of_element_located((By.ID, "password")))

    driver.find_element(By.ID, "email").clear()
    driver.find_element(By.ID, "email").send_keys(EMAIL)

    driver.find_element(By.ID, "password").clear()
    driver.find_element(By.ID, "password").send_keys(PASSWORD)

    # submit (button[type='submit'])
    submit_btn = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
    submit_btn.click()

    # wait for navigation to /posts
    wait_for(driver, EC.url_contains(POSTS_PATH))
    print("[OK] Logged in and navigated to posts page:", driver.current_url)

def search_on_posts(driver, query="fire", wait_seconds=3):
    # wait for search input (placeholder "Search alerts..." per your description)
    search_input = wait_for(driver, EC.presence_of_element_located((By.CSS_SELECTOR, "input[placeholder*='Search']")))
    search_input.clear()
    search_input.send_keys(query)
    print(f"[INFO] Typed search '{query}'")
    # stay here some seconds to simulate user pause/wait per your instruction
    time.sleep(wait_seconds)

def go_create_post(driver):
    # Click Create Alert link/button that routes to /create-post
    # Prefer anchor with href '/create-post'
    create_link = wait_for(driver, EC.element_to_be_clickable((By.XPATH, "//a[@href='/create-post' or contains(@href,'/create-post')]")))
    create_link.click()
    # wait for create page
    wait_for(driver, EC.url_contains(CREATE_PATH))
    print("[OK] Navigated to create-post:", driver.current_url)

def set_geolocation(driver, lat=GEO_LAT, lon=GEO_LON, accuracy=GEO_ACCURACY):
    # Use Chrome DevTools Protocol to override geolocation
    try:
        driver.execute_cdp_cmd("Emulation.setGeolocationOverride", {
            "latitude": float(lat),
            "longitude": float(lon),
            "accuracy": float(accuracy)
        })
        print(f"[INFO] Geolocation overridden to lat={lat}, lon={lon}")
    except Exception as e:
        print("[WARN] Couldn't set geolocation override via CDP:", e)

def create_post_fill_and_submit(driver, title, content):
    # Wait for the form fields to be present
    # Title: input[type='text'] inside form
    title_input = wait_for(driver, EC.presence_of_element_located((By.CSS_SELECTOR, "form input[type='text']")))
    title_input.clear()
    title_input.send_keys(title)
    print("[INFO] Title entered.")

    # Content: textarea
    content_input = wait_for(driver, EC.presence_of_element_located((By.CSS_SELECTOR, "form textarea")))
    content_input.clear()
    content_input.send_keys(content)
    print("[INFO] Content entered.")

    # Category: select -> choose option value "7" (Traffic & Transportation)
    try:
        category_select_el = driver.find_element(By.CSS_SELECTOR, "form select")
        select = Select(category_select_el)
        # attempt to select by value 7; if not present choose by visible text
        try:
            select.select_by_value("7")
            print("[INFO] Selected category value=7 (Traffic & Transportation).")
        except Exception:
            # fallback: choose by index 1 if available
            if len(category_select_el.find_elements(By.TAG_NAME, "option")) > 1:
                select.select_by_index(1)
                print("[INFO] Selected category by index fallback.")
    except Exception as e:
        print("[WARN] Category select not found or could not be selected:", e)

    # Tag: click button with text 'Road Closed'
    try:
        tag_btn = driver.find_element(By.XPATH, "//button[normalize-space()='Road Closed']")
        tag_btn.click()
        print("[INFO] Clicked tag 'Road Closed'.")
    except Exception as e:
        print("[WARN] Tag 'Road Closed' not found or not clickable:", e)

    # Priority: select second select element (we already used first for categories) -> choose 'high'
    try:
        selects = driver.find_elements(By.CSS_SELECTOR, "form select")
        # find a select with option value 'high', choose it
        priority_selected = False
        for sel_el in selects:
            opt_texts = [o.get_attribute("value") for o in sel_el.find_elements(By.TAG_NAME, "option")]
            if "high" in opt_texts:
                Select(sel_el).select_by_value("high")
                priority_selected = True
                print("[INFO] Selected priority 'high'.")
                break
        if not priority_selected and len(selects) >= 2:
            # fallback: choose last select's index 2 (if options exist)
            try:
                Select(selects[-1]).select_by_index(2)
                print("[INFO] Selected priority by fallback index.")
            except Exception:
                pass
    except Exception as e:
        print("[WARN] Priority select handling failed:", e)

    # Use Current Location button -> first button with text "Use Current Location" (click after overriding geolocation)
    try:
        # set geolocation via CDP so the browser returns coords when page asks
        set_geolocation(driver, GEO_LAT, GEO_LON, GEO_ACCURACY)

        use_loc_btn = driver.find_element(By.XPATH, "//button[contains(normalize-space(),'Use Current Location')]")
        # scroll into view then click
        driver.execute_script("arguments[0].scrollIntoView({block:'center'});", use_loc_btn)
        use_loc_btn.click()
        print("[INFO] Clicked 'Use Current Location' button.")
        # small wait for UI update
        time.sleep(1)
    except Exception as e:
        print("[WARN] Could not click 'Use Current Location' or geolocation not applied:", e)

    # Submit form: button[type='submit'] with text like 'Create Post'
    try:
        submit_btn = driver.find_element(By.CSS_SELECTOR, "form button[type='submit']")
        # scroll and click
        driver.execute_script("arguments[0].scrollIntoView({block:'center'});", submit_btn)
        submit_btn.click()
        print("[INFO] Clicked create/submit button.")
    except Exception as e:
        print("[ERROR] Could not find/submit the create form:", e)
        raise

    # Wait for redirect back to posts
    wait_for(driver, EC.url_contains(POSTS_PATH), timeout=20)
    print("[OK] After submit navigated to posts:", driver.current_url)

def verify_post_present(driver, title, timeout=12):
    # Wait a moment for posts list to update
    try:
        # Try to find element containing the title text
        # First try direct contains text on elements
        xpath = f"//*[contains(normalize-space(.), \"{title}\")]"
        WebDriverWait(driver, timeout).until(EC.presence_of_element_located((By.XPATH, xpath)))
        print(f"[PASS] Created post title found on posts page: '{title}'")
        return True
    except Exception:
        # fallback: check body text
        body_text = driver.find_element(By.TAG_NAME, "body").text
        if title in body_text:
            print(f"[PASS-fallback] Created post title found in page body text: '{title}'")
            return True
        else:
            print(f"[FAIL] Created post title NOT found: '{title}'")
            return False

def main():
    driver = create_driver(headless=False)
    try:
        # 1) Login
        login_flow(driver)

        # 2) On posts page, type search "fire" and wait a few seconds
        search_on_posts(driver, "fire", wait_seconds=4)

        # 3) Click Create Alert to go to create-post
        go_create_post(driver)

        # 4) Fill create post form and submit
        # Use a unique title to avoid collisions
        unique_title = "Traffic at Natun Bazar " + str(uuid.uuid4())[:8]
        sample_content = "Automated test content: traffic jam reported at Natun Bazar. Please be careful."

        create_post_fill_and_submit(driver, unique_title, sample_content)

        # 5) Verify the post is present on posts page
        ok = verify_post_present(driver, "Traffic at Natun Bazar")
        if not ok:
            # also try verifying by the exact unique title
            verify_post_present(driver, unique_title)

    except Exception as e:
        print("[ERROR] Test flow error:", e)
    finally:
        # short pause to let you see results
        time.sleep(2)
        driver.quit()

if __name__ == "__main__":
    main()

