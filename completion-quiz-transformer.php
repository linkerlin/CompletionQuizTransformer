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

// 添加在主函数开头
function completion_quiz_transformer_content($content) {
    // 如果不是文章内容，直接返回
    if (!is_singular('post')) {
        return $content;
    }

    // 添加调试模式检查
    $debug = defined('WP_DEBUG') && WP_DEBUG;
    if ($debug) {
        error_log('Completion Quiz: 开始处理文章内容');
    }
    
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
        $escaped_text = esc_attr($text);
        
        error_log('转义后的文本: ' . $escaped_text);

        // 替换为填空题的 HTML，确保使用单引号包裹属性值
        return sprintf(
            '<span class="completion-quiz" data-answer="%s">
                <span class="fill-blank" contenteditable="true">_____</span>
            </span>',
            $escaped_text
        );
    }, $content);

    // 将内容按段落分割
    $paragraphs = explode('</p>', $content);
    
    // 处理每个段落
    foreach ($paragraphs as &$paragraph) {
        // 如果段落中包含填空题
        if (strpos($paragraph, 'completion-quiz') !== false) {
            // 在段落末尾添加按钮
            $paragraph .= '<div class="completion-quiz-controls">
                            <button class="show-answers">显</button>
                            <button class="clear-answers">清</button>
                        </div>';
        }
        // 重新添加段落结束标签（除非是最后一个空段落）
        if (trim($paragraph) !== '') {
            $paragraph .= '</p>';
        }
    }

    // 重新组合内容
    $content = implode('', $paragraphs);

    return $content;
}

// 加载脚本和样式
add_action('wp_enqueue_scripts', 'completion_quiz_enqueue_scripts');

function completion_quiz_enqueue_scripts() {
    wp_enqueue_script('completion-quiz-script', plugins_url('completion-quiz.js', __FILE__), ['jquery'], '1.0', true);
    wp_enqueue_style('completion-quiz-style', plugins_url('completion-quiz.css', __FILE__));
}


