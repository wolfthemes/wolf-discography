<?/**
 *
 *
 *
 * @param type $param description
 * @return type description
 */

class PostRenderer {
    public function render($data, $atts) {
        // 1. Try theme templates first (for custom themes)
        if ($theme_template = $this->locate_theme_template($atts['display'])) {
            return $this->render_with_theme_template($data, $atts, $theme_template);
        }

        // 2. Fall back to built-in templates (for standalone use)
        return $this->render_with_builtin_template($data, $atts);
    }

    private function locate_theme_template($display_type) {
        // Check if theme has custom templates
        return locate_template([
            "wolf-discography/content-{$display_type}.php",
            "wolf-discography/content.php"
        ]);
    }
}