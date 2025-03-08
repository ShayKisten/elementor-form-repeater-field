# Elementor Form Repeater Field

**Description:**

Elementor Form Repeater Field is a WordPress plugin that enhances Elementor Pro forms by adding a "Repeater Container" field type. This allows users to create dynamic, repeatable sections within forms, such as collecting multiple user details with nested email fields. The plugin provides a seamless frontend interface for adding, removing, and collapsing sections, with all data processed for easy display.

**Key Features:**

* **Repeatable Sections:** Define blocks of form fields that users can duplicate on the frontend.
* **Nested Repeaters:** Supports nested repeatable sections.
* **Collapsible Sections:** Users can expand or collapse repeater sections for better usability.
* **Dynamic Field Naming:** Automatically generates unique field names (e.g., `field_0`, `field_1_repeater_0`) for submission.
* **Add/Remove Controls:** Includes "Add More" buttons to duplicate sections and delete buttons to remove them.
* **Processed Output:** Formats repeater data into readable labels (e.g., "Repeater 1 - Field 1 - Field") for post-submission display.
* **Lightweight Integration:** Loads assets only on Elementor-built pages for performance.
* **Required Fields:** All fields inside a repeater block is set to required.

**Installation:**

1. Download the plugin ZIP file.
2. In your WordPress admin panel, navigate to "Plugins" > "Add New."
3. Click "Upload Plugin" and select the downloaded ZIP file.
4. Click "Install Now" and then "Activate Plugin."

**Usage:**

1. Open an Elementor Pro form in the Elementor editor.
2. Add a "Repeater Container" field:
   - Set "Repeater Type" to "Open" to start a section, and "Close" to end it.
   - Use the same "Repeater Identifier" for matching "Open" and "Close" pairs.
   - Add a "Title" for "Open" containers.
   - Set "Button Text" for "Close" containers (e.g., "Add More").
3. Place regular form fields (e.g., Text for "Name", Email for "Email Address") between "Open" and "Close" containers.
4. Save and preview the form:
   - Users can click "Add More" to duplicate sections.
   - Use the toggle (▼/▶) to collapse/expand sections.
   - Click ✕ to delete sections.
5. Submit the form to see processed data.

**Configuration:**

* No additional settings are required—just add "Repeater Container" fields to your Elementor Pro form and configure their identifiers and titles.

**Changelog:**

* **1.0.1:**
  * Made all fields inside a repeater block required

* **1.0.0:**
  * Initial release.
  * Adds "Repeater Container" field type to Elementor Pro forms.
  * Supports repeatable and nested sections with dynamic naming.
  * Includes collapsible UI and add/remove functionality.
  * Processes repeater data for readable display post-submission.

**Future Enhancements:**

* Add a maximum section limit option in the field settings.
* Improve title formatting for nested repeater fields.
* Add support for custom validation rules within repeater sections.
* Enhance styling options via a settings panel or CSS variables.
* Integrate with Elementor’s form actions for better compatibility with third-party plugins.

**License:**

This plugin is licensed under the GPL-2.0+ license.

**Author:**

ShayKisten