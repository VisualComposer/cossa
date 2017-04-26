<?php
/**
 * Plugin Name: Content Style Saviour
 * Plugin URI: http://visualcomposer.io/deactivate/
 * Description: Automatically loads saved CSS for the content.
 *
 * Version: 1.0
 * Author: WPBakery
 * Author URI: http://wpbakery.com
 * Requires at least: 4.1
 */
 
/**
 * Check for direct call file.
 */
if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

if (defined('VCV_VERSION')) {
   return;
}

class VcwbEnqueueController
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueGlobalAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueSourceAssets']);
    }

    public function enqueueGlobalAssets()
    {
        $bundleUrl = get_option('vcv-globalElementsCssFileUrl');
        if ($bundleUrl) {
            wp_enqueue_style('vcv:assets:global:styles:' . $this->slugify($bundleUrl), $bundleUrl);
        }
    }

    public function enqueueSourceAssets()
    {
        $sourceId = get_the_ID();
        $bundleUrl = get_post_meta($sourceId, 'vcvSourceCssFileUrl', true);
        if ($bundleUrl) {
            wp_enqueue_style('vcv:assets:source:main:styles:' . $this->slugify($bundleUrl), $bundleUrl);
        }
    }

    public function enqueueAssets()
    {
        $sourceId = get_the_ID();
        $assetsFiles = get_post_meta($sourceId, 'vcvSourceAssetsFiles', true);

        if (!is_array($assetsFiles)) {
            return;
        }

        if (isset($assetsFiles['cssBundles']) && is_array($assetsFiles['cssBundles'])) {
            foreach ($assetsFiles['cssBundles'] as $asset) {
                wp_enqueue_style('vcv:assets:source:styles:' . $this->slugify($asset), $asset);
            }
            unset($asset);
        }

        if (isset($assetsFiles['jsBundles']) && is_array($assetsFiles['jsBundles'])) {
            foreach ($assetsFiles['jsBundles'] as $asset) {
                wp_enqueue_script('vcv:assets:source:scripts:' . $this->slugify($asset), $asset);
            }
            unset($asset);
        }
    }

    public function slugify($str)
    {
        $str = strtolower($str);
        $str = html_entity_decode($str);
        $str = preg_replace('/[^\w\s]+/', '', $str);
        $str = preg_replace('/\s+/', '-', $str);

        return $str;
    }
}

new VcwbEnqueueController();
