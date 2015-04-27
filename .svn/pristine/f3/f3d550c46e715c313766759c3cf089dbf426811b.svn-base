/**
 * Author: jason_chen(chenwei)
 * Date: 12-9-28
 */

$(function (){
    function changeTextNum(box) {
        var box=$(box);
        box.each(function (){
           var textArea=$(this).find('textarea'),
            textNum=$(this).find('.text-num');
            textArea.keyup(function (e) {
                if ($(this).val().length > 120) {
                    $(this).val($(this).val().substring(0, 120));
                    textNum.html(0);
                } else {
                    textNum.html(120 - ($(this).val().length));
                }
            });
        })
    }
    changeTextNum('.box-commit');
})
