(function() {

    var iblockId = parse("IBLOCK_ID");
    var elementId = parse("ID");
    var url;

    if (iblockId == 6) {
        url = '/house/copy/';
    }

    if (iblockId == 7) {
        url = '/entrance/copy/';
    }

    if (iblockId == 8) {
        url = '/floor/copy/';
    }

    if (iblockId == 9) {
        url = '/flat/copy/';
    }

    $(function() {
        if (window.location.pathname != '/bitrix/admin/iblock_element_edit.php') return; //Ничего не делаем в других страницах админки
        $nameField = $("#tr_NAME");

        addButton();
    });

    /**
    * Добавить кнопку
    */
    function addButton() {
        var $button = $('<a>', {
            href: 'javascript:void(0);',
            text: '[Копировать элемент с содержимым]',
            style: 'margin-left: 20px'
        });
        $nameField.find('input[type="text"]').after($button);

        $button.on('click', function() {

            if (confirm("Вы действительно хотите скопировать элемент?")) {
                copyElements();
            }

            return false;
        });
    }

    function copyElements() {
        $.ajax({
            url: url,
            data: {'elementId': elementId},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                window.location.href = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=' + iblockId + '&type=object&ID=' + data.id;
            }
        });
    }

    function parse(val) {
        var result = false,
            tmp = [];
        location.search
            //.replace ( "?", "" )
            // this is better, there might be a question mark inside
            .substr(1)
            .split("&")
            .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
            });
        return result;
    }

})();