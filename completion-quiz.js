jQuery(document).ready(function ($) {
    if (typeof jQuery === 'undefined') {
        console.error('Completion Quiz: jQuery is not loaded');
        return;
    }

    // 用户点击空白填空区域，允许输入
    $(document).on('click', '.fill-blank', function () {
        const $this = $(this);
        if ($this.text().trim() === '_____') {
            $this.text('');  // 清空默认的下划线
        }
        $this.focus();
    });

    // 用户按下回车键后验证答案
    $(document).on('keypress', '.fill-blank', function (e) {
        try {
            if (e.which === 13) { // 回车键
                e.preventDefault();
                const $this = $(this);
                const $quizContainer = $this.closest('.completion-quiz');
                const userAnswer = $this.text().trim();
                const correctAnswer = $quizContainer.attr('data-answer');

                console.log('按下回车键');
                console.log('用户输入:', userAnswer);
                console.log('答案容器HTML:', $quizContainer.prop('outerHTML'));
                console.log('正确答案 (通过attr):', correctAnswer);

                // 确保有正确答案
                if (!correctAnswer) {
                    console.error('错误：未能获取到正确答案');
                    console.log('完整的答案容器:', $quizContainer[0]);
                    return;
                }

                // 移除之前的反馈
                $this.siblings('.feedback, .correct-answer').remove();

                // 清理用户输入和正确答案用于比较（只保留字母和中文字符）
                const cleanUserAnswer = userAnswer.replace(/[^a-zA-Z\u4e00-\u9fa5]/g, '').toLowerCase();
                const cleanCorrectAnswer = correctAnswer.replace(/[^a-zA-Z\u4e00-\u9fa5]/g, '').toLowerCase();

                if (cleanUserAnswer === cleanCorrectAnswer) {
                    console.log('答案正确！');
                    $this.css('background-color', 'lightgreen')
                         .after('<span class="feedback">✅</span>');
                } else {
                    console.log('答案错误，将在5秒后显示正确答案');
                    $this.css('background-color', 'pink')
                         .after('<span class="feedback">❌</span>');
                    
                    setTimeout(() => {
                        console.log('开始显示正确答案');
                        
                        // 清理所有反馈标记
                        $this.siblings('.feedback, .correct-answer').remove();
                        
                        // 设置正确答案（显示完整的原始答案）
                        $this.text(correctAnswer)
                            .css('background-color', '');
                        
                        console.log('已设置答案到填空处');
                        console.log('当前填空内容:', $this.text());
                    }, 5000);
                }
            }
        } catch (error) {
            console.error('Completion Quiz Error:', error);
        }
    });

    // 显示答案按钮的处理
    $(document).on('click', '.show-answers', function () {
        console.log('点击显示答案按钮');
        $('.completion-quiz').each(function () {
            const answer = $(this).data('answer');
            console.log('显示答案:', answer);
            $(this).find('.fill-blank').text(answer);
        });
    });

    // 清除答案按钮的处理
    $(document).on('click', '.clear-answers', function () {
        console.log('点击清除答案按钮');
        $('.completion-quiz').each(function () {
            $(this).find('.fill-blank').text('_____').css('background-color', '');
            $(this).find('.feedback, .correct-answer').remove();
        });
    });
}); 