<?php

function smarty_function_ngettext($params, &$smarty)
{
    $msgid1 = $params['msgid1'];
    $msgid2 = $params['msgid2'];
    $n = $params['n'];

	//return ngettext($msgid1, $msgid2, $n);
    if ($n == 1) {
        return $msgid1;
    } else {
        return $msgid2;
    }

}

