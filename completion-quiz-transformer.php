<?php
/**
 * Plugin Name: Completion Quiz Transformer
 * Description: 自动将文章正文中的关键语句改造成填空题，通过交互完成完形填空功能。
 * Version: 1.2
 * Author: Halo Master
 */

if (!defined('ABSPATH')) {
    exit; // 防止直接访问
}

// 插件主过滤器：修改文章内容
add_filter('the_content', 'completion_quiz_transformer_content');

function completion_quiz_transformer_content($content) {
    // 正则匹配斜体或粗体内容
    $pattern = '/<em>(.*?)<\/em>|<strong>(.*?)<\/strong>/i';

    // 添加调试输出
    error_log('开始处理内容转换');

    // 替换逻辑
    $content = preg_replace_callback($pattern, function ($matches) {
        // 提取内容
        $text = !empty($matches[1]) ? $matches[1] : $matches[2];
        
        // 调试输出
        error_log('找到需要转换的文本: ' . $text);
        
        // 确保文本被正确转义
        $escaped_text = esc_attr($text); // 改用 esc_attr 替代 esc_js
        
        error_log('转义后的文本: ' . $escaped_text);

        // 替换为填空题的 HTML，确保使用单引号包裹属性值
        return sprintf(
            '<span class="completion-quiz" data-answer="%s">
                <span class="fill-blank" contenteditable="true">_____</span>
            </span>',
            $escaped_text
        );
    }, $content);

    // 在每段落后面添加显示答案和清除答案按钮
    $content .= '<div class="completion-quiz-controls">
                    <button class="show-answers">显示答案</button>
                    <button class="clear-answers">清除答案</button>
                </div>';

    return $content;
}

// 加载脚本和样式
add_action('wp_enqueue_scripts', 'completion_quiz_enqueue_scripts');

function completion_quiz_enqueue_scripts() {
    wp_enqueue_script('completion-quiz-script', plugins_url('completion-quiz.js', __FILE__), ['jquery'], '1.0', true);
    wp_enqueue_style('completion-quiz-style', plugins_url('completion-quiz.css', __FILE__));
}


