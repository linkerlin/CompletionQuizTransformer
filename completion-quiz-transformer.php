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

    // 替换逻辑
    $content = preg_replace_callback($pattern, function ($matches) {
        // 提取内容
        $text = $matches[1] ?? $matches[2];
        $escaped_text = esc_js($text); // 转义用于 JavaScript 的文本

        // 替换为填空题的 HTML
        return "<span class='completion-quiz' data-answer='{$escaped_text}'>
                    <span class='fill-blank' contenteditable='true'>_____</span>
                </span>";
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

// 创建 JavaScript 文件
add_action('wp_footer', 'completion_quiz_inline_script');
function completion_quiz_inline_script() {
    ?>
    <script>
        jQuery(document).ready(function ($) {
            // 用户点击空白填空区域，允许输入
            $(document).on('click', '.fill-blank', function () {
                $(this).focus();
            });

            // 用户按下回车键后验证答案
            $(document).on('keypress', '.fill-blank', function (e) {
                if (e.which === 13) { // 回车键
                    e.preventDefault();
                    const $this = $(this);
                    const userAnswer = $this.text().trim();
                    const correctAnswer = $this.closest('.completion-quiz').data('answer');

                    // 移除之前的反馈
                    $this.siblings('.feedback').remove();

                    if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                        $this.css('background-color', 'lightgreen')
                             .after('<span class="feedback">√</span>')
                             .text(correctAnswer);  // 显示正确的大小写形式
                    } else {
                        $this.css('background-color', 'pink')
                             .after('<span class="feedback">×</span>');
                        
                        // 5秒后自动填入正确答案
                        setTimeout(() => {
                            $this.text(correctAnswer)  // 填入正确答案
                                .css('background-color', '');  // 移除背景色
                            $this.siblings('.feedback').remove();  // 移除对错标记
                        }, 5000);
                    }
                }
            });

            // 显示答案
            $(document).on('click', '.show-answers', function () {
                $('.completion-quiz').each(function () {
                    const answer = $(this).data('answer');
                    $(this).find('.fill-blank').text(answer);
                });
            });

            // 清除答案
            $(document).on('click', '.clear-answers', function () {
                $('.completion-quiz').each(function () {
                    $(this).find('.fill-blank').text('_____').css('background-color', '');
                    $(this).find('.feedback').remove();
                });
            });
        });
    </script>
    <?php
}

// 创建 CSS 样式
add_action('wp_head', 'completion_quiz_inline_style');
function completion_quiz_inline_style() {
    ?>
    <style>
        .completion-quiz {
            display: inline-block;
            position: relative;
        }

        .fill-blank {
            display: inline-block;
            min-width: 50px;
            border-bottom: 2px dashed #000;
            cursor: pointer;
        }

        .fill-blank:focus {
            outline: none;
            border-bottom: 2px solid #000;
        }

        .feedback {
            margin-left: 5px;
            font-weight: bold;
            color: #000;
        }

        .completion-quiz-controls {
            margin-top: 20px;
        }

        .completion-quiz-controls button {
            margin-right: 10px;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
    <?php
}