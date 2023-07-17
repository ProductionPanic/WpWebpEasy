<?php

namespace ProductionPanic\WebpEasy\Backend\Wp;

use ProductionPanic\WebpEasy\Backend\Wp\Enums\Dashicons;

abstract class AdminPage extends HasWpActions
{
    protected $root_menu_page;
    protected $sub_menu_items = array();
    private Dashicons $icon;
    private static array $registered = [];
    private static array $instances = [];
    private array $enque_scripts = array();
    private array $enque_styles = array();


    final private function __construct()
    {
        parent::__construct();
        $this->icon = Dashicons::ADMIN_SETTINGS;
        add_action('admin_menu', function () {
            $this->register_menu_page();
        });

        add_action('admin_enqueue_scripts', function () {
            // check if page is current page
            $current_page = $_GET['page'] ?? null;
            if (!$this->is_page_this($current_page)) {
                return;
            }


            foreach ($this->enque_scripts as $script) {
                $handle = $script['handle'];
                $src = $script['src'];

                wp_register_script($handle, $src);

                wp_enqueue_script($handle);
            }
            foreach ($this->enque_styles as $style) {
                $handle = $style['handle'];
                $src = $style['src'];

                wp_register_style($handle, $src);

                wp_enqueue_style($handle);
            }
        });

        try {
            $this->setUp();
        } catch(\Throwable $e) {
        }
        
    }

    protected function enqueue_script(string $handle, string $src)
    {
        $this->enque_scripts[] = array(
            'handle' => $handle,
            'src' => $src
        );
    }

    protected function enqueue_style(string $handle, string $src)
    {
        $this->enque_styles[] = array(
            'handle' => $handle,
            'src' => $src
        );
    }

    protected function set_icon(Dashicons $icon)
    {
        $this->icon = $icon;
    }

    public static function init():static
    {
        // get class extending this class
        $class = get_called_class();
        // if class is not already registered as admin page register it
        if (!in_array($class, self::$registered)) {
            self::$registered[] = $class;
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }

    public function setRootMenuPage($title)
    {
        $this->root_menu_page = $title;
    }

    public function addSubMenuItem(string $title, string $slug, $hidden = false)
    {
        $this->sub_menu_items[] = array(
            'title' => $title,
            'page_slug' => $slug,
            'hidden' => $hidden
        );
    }

    private function slugify(string $string): string
    {
        return strtolower(str_replace(' ', '_', $string));
    }

    private function register_menu_page()
    {
        $slug = $this->slugify($this->root_menu_page);
        add_menu_page(
            $this->root_menu_page,
            $this->root_menu_page,
            'manage_options',
            $slug,
            array($this, 'render_page'),
            icon_url: $this->icon->value,
        );

        $has_main = false;
        foreach ($this->sub_menu_items as $sub_menu_item) {
            if ($slug === $sub_menu_item['page_slug']) {
                $has_main = true;
            }
            add_submenu_page(
                $slug,
                $sub_menu_item['title'],
                $sub_menu_item['title'],
                'manage_options',
                $sub_menu_item['page_slug'],
                array($this, 'render_page')
            );
            if ($sub_menu_item['hidden'] && isset($_GET['page']) && $_GET['page'] !== $sub_menu_item['page_slug']) {
                remove_submenu_page($slug, $sub_menu_item['page_slug']);
            }
        }

        if (!$has_main) {
            // remove main menu page if it is not specifically added as sub menu item
            remove_submenu_page($slug, $slug);
        }
    }

    public function render_page()
    {
        $page_slug = str_replace('-', '_', $_GET['page']);
        $method_name = 'do_' . $page_slug;

        if (method_exists($this, $method_name)) {
            global $SENDY_CUR_PAGE;
            $SENDY_CUR_PAGE = $page_slug;
            $this->$method_name();
        }
    }

    private function is_page_this($page_slug): bool
    {
        if (!is_string($page_slug)) {
            return false;
        }
        $all_pages = array();
        $all_pages[] = $this->slugify($this->root_menu_page);
        foreach ($this->sub_menu_items as $sub_menu_item) {
            $all_pages[] = $this->slugify($sub_menu_item['page_slug']);
        }

        return in_array($page_slug, $all_pages);
    }

    abstract public function setUp();
}
