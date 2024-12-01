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
            const $quizContainer = $this.closest('.completion-quiz');
            const userAnswer = $this.text().trim();
            const correctAnswer = $quizContainer.attr('data-answer');

            console.log('按下回车键');
            console.log('用户输入:', userAnswer);
            console.log('答案容器HTML:', $quizContainer.prop('outerHTML'));
            console.log('正确答案 (通过attr):', correctAnswer);
            console.log('正确答案 (通过data):', $quizContainer.data('answer'));

            // 确保有正确答案
            if (!correctAnswer) {
                console.error('错误：未能获取到正确答案');
                console.log('完整的答案容器:', $quizContainer[0]);
                return;
            }

            // 移除之前的反馈
            $this.siblings('.feedback, .correct-answer').remove();

            if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                console.log('答案正确！');
                $this.css('background-color', 'lightgreen')
                     .after('<span class="feedback">√</span>');
            } else {
                console.log('答案错误，将在5秒后显示正确答案');
                $this.css('background-color', 'pink')
                     .after('<span class="feedback">×</span>');
                
                setTimeout(() => {
                    console.log('开始显示正确答案');
                    
                    // 清理所有反馈标记
                    $this.siblings('.feedback, .correct-answer').remove();
                    
                    // 设置正确答案
                    $this.text(correctAnswer)
                        .css('background-color', '');
                    
                    console.log('已设置答案到填空处');
                    console.log('当前填空内容:', $this.text());
                }, 5000);
            }
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