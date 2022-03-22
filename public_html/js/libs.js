function time_ago ( time )
{

    switch ( typeof time )
    {
        case 'number':
            break;
        case 'string':
            time = +new Date( time );
            break;
        case 'object':
            if ( time.constructor === Date ) time = time.getTime();
            break;
        default:
            time = +new Date();
    }
    var time_formats = [
        [ 60, '秒', 1 ], // 60
        [ 120, '1分前', '1 minute from now' ], // 60*2
        [ 3600, '分', 60 ], // 60*60, 60
        [ 7200, '1時間前', '1 hour from now' ], // 60*60*2
        [ 86400, '時間', 3600 ], // 60*60*24, 60*60
        [ 172800, '1日間前', 'Tomorrow' ], // 60*60*24*2
        [ 604800, '日間', 86400 ], // 60*60*24*7, 60*60*24
        [ 1209600, '１週間前', 'Next week' ], // 60*60*24*7*4*2
        [ 2419200, '週間', 604800 ], // 60*60*24*7*4, 60*60*24*7
        [ 4838400, '１ヶ月前', 'Next month' ], // 60*60*24*7*4*2
        [ 29030400, 'ヶ月', 2419200 ], // 60*60*24*7*4*12, 60*60*24*7*4
        [ 58060800, '１年間前', 'Next year' ], // 60*60*24*7*4*12*2
        [ 2903040000, '年間', 29030400 ], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
    ];
    var seconds = ( +new Date() - time ) / 1000,
        token = '前',
        list_choice = 1;

    if ( seconds == 0 )
    {
        return '数秒前'
    }
    if ( seconds < 0 )
    {
        seconds = Math.abs( seconds );
        token = '後';
        list_choice = 2;
    }
    var i = 0,
        format;
    while ( format = time_formats[ i++ ] )
        if ( seconds < format[ 0 ] )
        {
            if ( typeof format[ 2 ] == 'string' )
                return format[ list_choice ];
            else
                return Math.floor( seconds / format[ 2 ] ) + format[ 1 ] + token;
        }
    return time;
}
