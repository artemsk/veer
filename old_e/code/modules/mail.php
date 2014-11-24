<?php

## Более удобная отправка почты.
// Функция отправляет письмо, полностью заданное в параметре $mail.
// Корректно обрабатываются заголовки To и Subject.
// TODO: переписать всю форму отправки писем!

function mailx($mail) {
    Debug::log();
    // Разделяем тело сообщения и заголовки.
    list ($head, $body) = preg_split("/\r?\n\r?\n/s", $mail, 2);
    // Выделяем заголовок To.
    $to = "";
    if (preg_match('/^To:\s*([^\r\n]*)[\r\n]*/m', $head, $p)) {
        $to = @$p[1]; // сохраняем
        $head = str_replace($p[0], "", $head); // удаляем из исходной строки
    }
    // Выделяем Subject.
    $subject = "";
    if (preg_match('/^Subject:\s*([^\r\n]*)[\r\n]*/m', $head, $p)) {
        $subject = @$p[1];
        $head = str_replace($p[0], "", $head);
    }
    // Отправляем почту. Внимание! Опасный прием!
    mail($to, $subject, $body, trim($head));
}

## Кодирование заголовков письма.
// Корректно кодирует все заголовки в письме $mail с использованием
// метода base64. Кодировка письма определяется автоматически на основе
// заголовка Content-type. Возвращает полученное письмо.

function mailenc($mail) {
    Debug::log();
    // Разделяем тело сообщения и заголовки.
    list ($head, $body) = preg_split("/\r?\n\r?\n/s", $mail, 2);
    // Определяем кодировку письма по заголовку Content-type.
    $encoding = '';
    $re = '/^Content-type:\s*\S+\s*;\s*charset\s*=\s*(\S+)/mi';
    if (preg_match($re, $head, $p))
        $encoding = $p[1];
    // Проходимся по всем строкам-заголовкам.
    $newhead = "";
    foreach (preg_split('/\r?\n/s', $head) as $line) {
        // Кодируем очередной заголовок.
        $line = mailenc_header($line, $encoding);
        $newhead .= "$line\r\n";
    }
    // Формируем окончательный результат.
    return "$newhead\r\n$body";
}

// Кодирует в строке максимально возможную последовательность
// символов, начинающуюся с недопустимого символа и НЕ
// включающую E-mail (адреса E-mail обрамляют символами < и >).
// Если в строке нет ни одного недопустимого символа, преобразование
// не производится.
function mailenc_header($header, $encoding) {
    Debug::log();
    // Кодировка не задана - делать нечего.
    if (!$encoding)
        return $header;
    // Сохраняем кодировку в глобальной переменной. Без использования
    // ООП это - единственный способ передать дополнительный параметр
    // callback-функции.
    $GLOBALS['mail_enc_header_encoding'] = $encoding;
    return preg_replace_callback(
            '/([\x7F-\xFF][^<>\r\n]*)/s', 'mailenc_header_callback', $header
    );
}

// Служебная функция для использования в preg_replace_callback().
function mailenc_header_callback($p) {
    Debug::log();
    $encoding = $GLOBALS['mail_enc_header_encoding'];
    // Пробелы в конце оставляем незакодированными.
    preg_match('/^(.*?)(\s*)$/s', $p[1], $sp);
    return "=?$encoding?B?" . base64_encode($sp[1]) . "?=" . $sp[2];
}

?>