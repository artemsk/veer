<?php

## ����� ������� �������� �����.
// ������� ���������� ������, ��������� �������� � ��������� $mail.
// ��������� �������������� ��������� To � Subject.
// TODO: ���������� ��� ����� �������� �����!

function mailx($mail) {
    Debug::log();
    // ��������� ���� ��������� � ���������.
    list ($head, $body) = preg_split("/\r?\n\r?\n/s", $mail, 2);
    // �������� ��������� To.
    $to = "";
    if (preg_match('/^To:\s*([^\r\n]*)[\r\n]*/m', $head, $p)) {
        $to = @$p[1]; // ���������
        $head = str_replace($p[0], "", $head); // ������� �� �������� ������
    }
    // �������� Subject.
    $subject = "";
    if (preg_match('/^Subject:\s*([^\r\n]*)[\r\n]*/m', $head, $p)) {
        $subject = @$p[1];
        $head = str_replace($p[0], "", $head);
    }
    // ���������� �����. ��������! ������� �����!
    mail($to, $subject, $body, trim($head));
}

## ����������� ���������� ������.
// ��������� �������� ��� ��������� � ������ $mail � ��������������
// ������ base64. ��������� ������ ������������ ������������� �� ������
// ��������� Content-type. ���������� ���������� ������.

function mailenc($mail) {
    Debug::log();
    // ��������� ���� ��������� � ���������.
    list ($head, $body) = preg_split("/\r?\n\r?\n/s", $mail, 2);
    // ���������� ��������� ������ �� ��������� Content-type.
    $encoding = '';
    $re = '/^Content-type:\s*\S+\s*;\s*charset\s*=\s*(\S+)/mi';
    if (preg_match($re, $head, $p))
        $encoding = $p[1];
    // ���������� �� ���� �������-����������.
    $newhead = "";
    foreach (preg_split('/\r?\n/s', $head) as $line) {
        // �������� ��������� ���������.
        $line = mailenc_header($line, $encoding);
        $newhead .= "$line\r\n";
    }
    // ��������� ������������� ���������.
    return "$newhead\r\n$body";
}

// �������� � ������ ����������� ��������� ������������������
// ��������, ������������ � ������������� ������� � ��
// ���������� E-mail (������ E-mail ��������� ��������� < � >).
// ���� � ������ ��� �� ������ ������������� �������, ��������������
// �� ������������.
function mailenc_header($header, $encoding) {
    Debug::log();
    // ��������� �� ������ - ������ ������.
    if (!$encoding)
        return $header;
    // ��������� ��������� � ���������� ����������. ��� �������������
    // ��� ��� - ������������ ������ �������� �������������� ��������
    // callback-�������.
    $GLOBALS['mail_enc_header_encoding'] = $encoding;
    return preg_replace_callback(
            '/([\x7F-\xFF][^<>\r\n]*)/s', 'mailenc_header_callback', $header
    );
}

// ��������� ������� ��� ������������� � preg_replace_callback().
function mailenc_header_callback($p) {
    Debug::log();
    $encoding = $GLOBALS['mail_enc_header_encoding'];
    // ������� � ����� ��������� �����������������.
    preg_match('/^(.*?)(\s*)$/s', $p[1], $sp);
    return "=?$encoding?B?" . base64_encode($sp[1]) . "?=" . $sp[2];
}

?>