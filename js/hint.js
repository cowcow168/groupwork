//初期設定
var red = '#f41214;';
var blue= '#1412f4;';
var bold = 'bold;';

function spanStyle(array){
	var mes = '';
	for(var k in array){
		mes += k + ':' + array[k];
	}
	return '<span style="' + mes + '">';
}

function endSpanStyle(){
	return '</span>';
}

$('body').css('position', 'relative');
//タイムカード　年月指定
$(function(){
    var moveElm = $('.hint_timecard_date'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_time_card_date = spanStyle({'color':red, 'font-weight':bold}) + 'クリックして表示させたい年月を選択' + endSpanStyle() + 'してください。<br>年をクリックすると年単位で指定が可能となります。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '選択したあとは表示ボタンを押下' + endSpanStyle() + 'してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; left: 20%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_date">' + hint_time_card_date + '</div>').prependTo('body');
            $('.prep_hint_timecard_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_date').hide('slow');
		$('.prep_hint_timecard_date').delay(500).queue(function(){
			$('.prep_hint_timecard_date').remove();
		});
    });
});

//タイムカード　就業開始時刻
$(function(){
    var moveElm = $('.hint_timecard_time_card_work_start'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_time_card_work_start = '現在の現場の就業開始時間（定時）を入力してください。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '一か月の間に現場が変わった場合は前の現場の時間を入力 ' + endSpanStyle() + 'してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; left: 20%; width: 42rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_work_start">' + hint_timecard_time_card_work_start + '</div>').prependTo('body');
			$('.prep_hint_timecard_work_start').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_work_start').hide('slow');
		$('.prep_hint_timecard_work_start').delay(500).queue(function(){
			$('.prep_hint_timecard_work_start').remove();
		});
	});
});

//タイムカード　就業終了時刻
$(function(){
    var moveElm = $('.hint_timecard_time_card_work_end'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_time_card_work_end = '現在の現場の就業終了時間（定時）を入力してください。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '一か月の間に現場が変わった場合は前の現場の時間を入力 ' + endSpanStyle() + 'してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; left: 50%; width: 42rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_work_end">' + hint_timecard_time_card_work_end + '</div>').prependTo('body');
			$('.prep_hint_timecard_work_end').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_work_end').hide('slow');
		$('.prep_hint_timecard_work_end').delay(500).queue(function(){
			$('.prep_hint_timecard_work_end').remove();
		});
	});
});

//タイムカード　基準休憩時刻
$(function(){
    var moveElm = $('.hint_timecard_time_card_rest_time'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_time_card_rest_time = '現在の現場の休憩時間を入力してください。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '何時から、ではなく何時間休憩できるかを入力' + endSpanStyle() + 'して<br>ください。<br>';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; right: 5%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_rest_time">' + hint_timecard_time_card_rest_time + '</div>').prependTo('body');
			$('.prep_hint_timecard_rest_time').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_rest_time').hide('slow');
		$('.prep_hint_timecard_rest_time').delay(500).queue(function(){
			$('.prep_hint_timecard_rest_time').remove();
		});
	});
});

//タイムカード　基準休憩時刻
$(function(){
    var moveElm = $('.hint_timecard_time_card_rest_time'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_time_card_rest_time = '現在の現場の休憩時間を入力してください。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '何時から、ではなく何時間休憩できるかを入力' + endSpanStyle() + 'して<br>ください。<br>';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; right: 5%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_rest_time">' + hint_timecard_time_card_rest_time + '</div>').prependTo('body');
			$('.prep_hint_timecard_rest_time').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_rest_time').hide('slow');
		$('.prep_hint_timecard_rest_time').delay(500).queue(function(){
			$('.prep_hint_timecard_rest_time').remove();
		});
	});
});

//タイムカード　客先勤務　hint_timecard_customer
$(function(){
    var moveElm = $('.hint_timecard_customer'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_customer = '現場勤務の出社時間等を入力してください。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '休憩時間は何時から、ではなく何時間休憩したかを入力<BR>' + endSpanStyle() + 'してください。<br>一日のうち現場・本社ともに仕事をした場合は両方に記入してください。<br>現在仕様により現場・本社の時間が被るとエラーになります。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; left: 24%; width: 44rem; height: 15rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_customer">' + hint_timecard_customer + '</div>').prependTo('body');
			$('.prep_hint_timecard_customer').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_customer').hide('slow');
		$('.prep_hint_timecard_customer').delay(500).queue(function(){
			$('.prep_hint_timecard_customer').remove();
		});
	});
});

//タイムカード　本社勤務　hint_timecard_office
$(function(){
    var moveElm = $('.hint_timecard_office'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_office = '本社勤務の出社時間等を入力してください。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '休憩時間は何時から、ではなく何時間休憩したかを入力<BR>' + endSpanStyle() + 'してください。<br>一日のうち現場・本社ともに仕事をした場合は両方に記入してください。<br>現在仕様により現場・本社の時間が被るとエラーになります。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; left: 24%; width: 44rem; height: 15rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_office">' + hint_timecard_office + '</div>').prependTo('body');
			$('.prep_hint_timecard_office').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_office').hide('slow');
		$('.prep_hint_timecard_office').delay(500).queue(function(){
			$('.prep_hint_timecard_office').remove();
		});
	});
});

//タイムカード　種別　hint_timecard_type
$(function(){
    var moveElm = $('.hint_timecard_type'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_type = '時間通りの出社・退勤ではなかった場合や、その他特殊な業務の場合に入力してください。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '何を入力すればいいかわからない場合は石丸さん（y.ishimaru@xxxcorp.jp）へ連絡してください。' + endSpanStyle();
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; right: 16%; width: 40rem; height: 10rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_type">' + hint_timecard_type + '</div>').prependTo('body');
			$('.prep_hint_timecard_type').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_type').hide('slow');
		$('.prep_hint_timecard_type').delay(500).queue(function(){
			$('.prep_hint_timecard_type').remove();
		});
	});
});

//タイムカード　事由　hint_timecard_reason
$(function(){
    var moveElm = $('.hint_timecard_reason'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_reason = '種別に対する理由を入力してください。<br>例えば声優のライブの場合は私用、<br>片頭痛の場合は体調不良、<br>リーダーの熊野神社祈祷の場合は自社用、<br>全体会議の場合は帰社日です。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '電車遅延の場合は未入力で構いませんが備考に入力をしてください。' + endSpanStyle() + '<br>その他複雑に理由が入り乱れる場合は<br>備考欄に記載してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; right: 16%; width: 44rem; height: 20rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_reason">' + hint_timecard_reason + '</div>').prependTo('body');
			$('.prep_hint_timecard_reason').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_reason').hide('slow');
		$('.prep_hint_timecard_reason').delay(500).queue(function(){
			$('.prep_hint_timecard_reason').remove();
		});
	});
});

//タイムカード　欠勤連絡　hint_timecard_absensce
$(function(){
    var moveElm = $('.hint_timecard_absensce'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_absensce = '連絡状況を入力してください。<br>未連絡は基本ないとは思いますが・・・';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; right: 16%; width: 30rem; height: 7rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_absensce">' + hint_timecard_absensce + '</div>').prependTo('body');
			$('.prep_hint_timecard_absensce').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_absensce').hide('slow');
		$('.prep_hint_timecard_absensce').delay(500).queue(function(){
			$('.prep_hint_timecard_absensce').remove();
		});
	});
});

//タイムカード　備考　hint_timecard_memo
$(function(){
    var moveElm = $('.hint_timecard_memo'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_timecard_memo = '特記事項や詳細を記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 10%; right: 16%; width: 28rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_timecard_memo">' + hint_timecard_memo + '</div>').prependTo('body');
			$('.prep_hint_timecard_memo').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_timecard_memo').hide('slow');
		$('.prep_hint_timecard_memo').delay(500).queue(function(){
			$('.prep_hint_timecard_memo').remove();
		});
	});
});

/*===========================================================
これより　週報ページ
===========================================================*/

//週報　年月週指定
$(function(){
    var moveElm = $('.hint_report_date'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_date = spanStyle({'color':red, 'font-weight':bold}) + 'クリックして表示させたい年月を選択' + endSpanStyle() + 'してください。<br>年をクリックすると年単位で指定が可能となります。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '週まで選択したあとは表示ボタンを押下' + endSpanStyle() + 'してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 5%; left: 50%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_date">' + hint_report_date + '</div>').prependTo('body');
            $('.prep_hint_report_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_date').hide('slow');
		$('.prep_hint_report_date').delay(500).queue(function(){
			$('.prep_hint_report_date').remove();
		});
    });
});

//週報　保存ボタン
$(function(){
    var moveElm = $('.hint_report_save'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_date = 'こまめに保存しましょう(^O^)<br>保存されてないというバグ報告が定期的にくるので<br>保存ボタンを押下したら「保存されました」と<br>表示されるような機能も追加予定です。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 5%; left: 50%; width: 38rem; height: 10rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_date">' + hint_report_date + '</div>').prependTo('body');
            $('.prep_hint_report_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_date').hide('slow');
		$('.prep_hint_report_date').delay(500).queue(function(){
			$('.prep_hint_report_date').remove();
		});
    });
});

//週報　提出ボタン
$(function(){
    var moveElm = $('.hint_report_sbm'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_date = '週報提出時に押下してください。<br>月報については毎月自動でメールが来ますので<br>そちらに従ってください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 5%; left: 50%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_date">' + hint_report_date + '</div>').prependTo('body');
            $('.prep_hint_report_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_date').hide('slow');
		$('.prep_hint_report_date').delay(500).queue(function(){
			$('.prep_hint_report_date').remove();
		});
    });
});

//週報　承認ボタン
$(function(){
    var moveElm = $('.hint_report_agree'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_date = '遅くとも水曜の午後10時までには承認するようにしましょう。<br>' + spanStyle({'color':red, 'font-weight':bold}) + '承認後は書き込みの修正が出来なくなります。' + endSpanStyle();
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 5%; left: 50%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_date">' + hint_report_date + '</div>').prependTo('body');
            $('.prep_hint_report_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_date').hide('slow');
		$('.prep_hint_report_date').delay(500).queue(function(){
			$('.prep_hint_report_date').remove();
		});
    });
});

//週報　差戻ボタン
$(function(){
    var moveElm = $('.hint_report_remand'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_date = '差戻については「週報ページの抜け」の他に、<br>各種精算やタイムカードなど<br>抜け、誤字脱字がひどい場合などに使ってください。<br>差戻ボタンを押さないと週報ページについてはメンバーが編集出来ない状態になっています。<br>その他のページは実は差し戻さなくても編集出来ますので、リーダーの指導方法を尊重します。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 5%; left: 50%; width: 38rem; height: 16rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_date">' + hint_report_date + '</div>').prependTo('body');
            $('.prep_hint_report_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_date').hide('slow');
		$('.prep_hint_report_date').delay(500).queue(function(){
			$('.prep_hint_report_date').remove();
		});
    });
});

//週報　作業内容 hint_report_working_memo
$(function(){
    var moveElm = $('.hint_report_working_memo'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_working_memo = '当日の作業内容を書いてください。<br>詳細に書いてもらえるとリーダーは返事がしやすいです。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_working_memo">' + hint_report_working_memo + '</div>').prependTo('body');
            $('.prep_hint_report_working_memo').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_working_memo').hide('slow');
		$('.prep_hint_report_working_memo').delay(500).queue(function(){
			$('.prep_hint_report_working_memo').remove();
		});
    });
});

//週報　学んだこと欄 hint_report_studing_memo
$(function(){
    var moveElm = $('.hint_report_studing_memo'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_studing_memo = 'アカウント発行メールにも記載がありますが<br>3行以上書くようにしましょう。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; bottom: 50%; right: 20%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_studing_memo">' + hint_report_studing_memo + '</div>').prependTo('body');
            $('.prep_hint_report_studing_memo').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_studing_memo').hide('slow');
		$('.prep_hint_report_studing_memo').delay(500).queue(function(){
			$('.prep_hint_report_studing_memo').remove();
		});
    });
});

//週報　コメント欄 hint_report_report_comment
$(function(){
    var moveElm = $('.hint_report_report_comment'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_report_comment = 'アカウント発行メールにも記載がありますが<br>最低1行以上書きましょう。<br>こちらは業務に関することでもいいですし、<br>プライベートについて書いてもいいです。<br>お子さんの成長記録や子育て奮闘記もよく見ます♪';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; bottom: 45%; right: 20%; width: 38rem; height: 13rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_report_comment">' + hint_report_report_comment + '</div>').prependTo('body');
            $('.prep_hint_report_report_comment').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_report_comment').hide('slow');
		$('.prep_hint_report_report_comment').delay(500).queue(function(){
			$('.prep_hint_report_report_comment').remove();
		});
    });
});

//週報　リーダーコメント欄 hint_report_leader_comment
$(function(){
    var moveElm = $('.hint_report_leader_comment'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_leader_comment = '自分のリーダーから返事がきます。<br>時には厳しく、時には冗談で。<br>プログラムチームはサブマネからもここに記載されることがあります。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; bottom: 26%; right: 20%; width: 34rem height: 7rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_leader_comment">' + hint_report_leader_comment + '</div>').prependTo('body');
            $('.prep_hint_report_leader_comment').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_leader_comment').hide('slow');
		$('.prep_hint_report_leader_comment').delay(500).queue(function(){
			$('.prep_hint_report_leader_comment').remove();
		});
    });
});

//週報　社長欄 hint_report_president_comment
$(function(){
    var moveElm = $('.hint_report_president_comment'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_president_comment = '社長のみ書けるスペースです。<br>毎回何か書き込まれるわけではないですが要チェックや！';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; bottom: 17%; right: 20%; width: 41rem; height: 7rem; z-index: 1; background-color: rgba(230, 230, 240, 1); border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_president_comment">' + hint_report_president_comment + '</div>').prependTo('body');
            $('.prep_hint_report_president_comment').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_president_comment').hide('slow');
		$('.prep_hint_report_president_comment').delay(500).queue(function(){
			$('.prep_hint_report_president_comment').remove();
		});
    });
});

//週報　その他欄 hint_report_human_comment
$(function(){
    var moveElm = $('.hint_report_human_comment'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_report_human_comment = '上記以外の人からの返信です。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; bottom: 7%; right: 20%; width: 26rem; height: 5rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_report_human_comment">' + hint_report_human_comment + '</div>').prependTo('body');
            $('.prep_hint_report_human_comment').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_report_human_comment').hide('slow');
		$('.prep_hint_report_human_comment').delay(500).queue(function(){
			$('.prep_hint_report_human_comment').remove();
		});
    });
});

/*===========================================================
これより　各種精算ページ
===========================================================*/

//精算　年月指定
$(function(){
    var moveElm = $('.hint_pay_date'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_date = spanStyle({'color':red, 'font-weight':bold}) + 'クリックして表示させたい年月を選択' + endSpanStyle() + 'してください。<br>年をクリックすると年単位で指定が可能となります。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 15%; right: 40%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_date">' + hint_pay_date + '</div>').prependTo('body');
            $('.prep_hint_pay_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_date').hide('slow');
		$('.prep_hint_pay_date').delay(500).queue(function(){
			$('.prep_hint_pay_date').remove();
		});
    });
});

//精算　保存ボタン
$(function(){
    var moveElm = $('.hint_pay_save'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_save = 'こまめに保存しましょう(^O^)<br>保存されてないというバグ報告が定期的にくるので<br>保存ボタンを押下したら「保存されました」と<br>表示されるような機能も追加予定です。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 15%; right: 40%; width: 38rem; height: 10rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_save">' + hint_pay_save + '</div>').prependTo('body');
            $('.prep_hint_pay_save').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_save').hide('slow');
		$('.prep_hint_pay_save').delay(500).queue(function(){
			$('.prep_hint_pay_save').remove();
		});
    });
});

//精算　日付
$(function(){
    var moveElm = $('.hint_pay_table_date'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_table_date = '購入した日を入力してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 26rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_table_date">' + hint_pay_table_date + '</div>').prependTo('body');
            $('.prep_hint_pay_table_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_table_date').hide('slow');
		$('.prep_hint_pay_table_date').delay(500).queue(function(){
			$('.prep_hint_pay_table_date').remove();
		});
    });
});

//精算　目的
$(function(){
    var moveElm = $('.hint_pay_purpose'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_purpose = '購入理由に合ったものを選択してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 34rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_purpose">' + hint_pay_purpose + '</div>').prependTo('body');
            $('.prep_hint_pay_purpose').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_purpose').hide('slow');
		$('.prep_hint_pay_purpose').delay(500).queue(function(){
			$('.prep_hint_pay_purpose').remove();
		});
    });
});

//精算　種別
$(function(){
    var moveElm = $('.hint_pay_kind'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_kind = '通勤定期や現場、本社までの交通費に関しては未選択で構いません。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 38rem; height: 6rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_kind">' + hint_pay_kind + '</div>').prependTo('body');
            $('.prep_hint_pay_kind').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_kind').hide('slow');
		$('.prep_hint_pay_kind').delay(500).queue(function(){
			$('.prep_hint_pay_kind').remove();
		});
    });
});

//精算　利用期間/利用施設
$(function(){
    var moveElm = $('.hint_pay_commuting_facility'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_commuting_facility = 'JRやメトロ、高速バス等の経営会社、路線名を記入してください。<br>例）JR中央総武線';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 38rem; height: 9rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_commuting_facility">' + hint_pay_commuting_facility + '</div>').prependTo('body');
            $('.prep_hint_pay_commuting_facility').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_commuting_facility').hide('slow');
		$('.prep_hint_pay_commuting_facility').delay(500).queue(function(){
			$('.prep_hint_pay_commuting_facility').remove();
		});
    });
});

//精算　利用区間/最寄り駅
$(function(){
    var moveElm = $('.hint_pay_commuting_section'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_commuting_section = '例）本八幡駅/新宿駅<br>例）東京駅/名古屋駅';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 24rem; height: 6rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_commuting_section">' + hint_pay_commuting_section + '</div>').prependTo('body');
            $('.prep_hint_pay_commuting_section').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_commuting_section').hide('slow');
		$('.prep_hint_pay_commuting_section').delay(500).queue(function(){
			$('.prep_hint_pay_commuting_section').remove();
		});
    });
});

//精算　利用期間
$(function(){
    var moveElm = $('.hint_pay_period'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_period = '該当する交通機関をいつからいつまで使ったかを記入してください。<br>当日のみの場合は開始も終了も同じ日を指定してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 38rem; height: 11rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_period">' + hint_pay_period + '</div>').prependTo('body');
            $('.prep_hint_pay_period').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_period').hide('slow');
		$('.prep_hint_pay_period').delay(500).queue(function(){
			$('.prep_hint_pay_period').remove();
		});
    });
});

//精算　備考
$(function(){
    var moveElm = $('.hint_pay_memo'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_memo = '何か特記時刻があれば記入してください。<br>帰社の場合は理由がそれぞれあると思うので記入してください。<br>例）全体会議の為　など';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 34rem height: 7rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_memo">' + hint_pay_memo + '</div>').prependTo('body');
            $('.prep_hint_pay_memo').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_memo').hide('slow');
		$('.prep_hint_pay_memo').delay(500).queue(function(){
			$('.prep_hint_pay_memo').remove();
		});
    });
});

//精算　金額
$(function(){
    var moveElm = $('.hint_pay_price'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_price = '利用した金額を半角数字のみで記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 38rem; height: 4rem; z-index: 1; background-color: rgba(230, 230, 240, 1); border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_price">' + hint_pay_price + '</div>').prependTo('body');
            $('.prep_hint_pay_price').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_price').hide('slow');
		$('.prep_hint_pay_price').delay(500).queue(function(){
			$('.prep_hint_pay_price').remove();
		});
    });
});

//精算　領収書上長提出
$(function(){
    var moveElm = $('.hint_pay_receipt'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_receipt = '提出済か未提出かを選択してください。<br>月報提出までに提出できていない場合は未提出となります。<br>ファイルのアップロードでは提出扱いにはなりません。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 42rem; height: 8rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_receipt">' + hint_pay_receipt + '</div>').prependTo('body');
            $('.prep_hint_pay_receipt').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_receipt').hide('slow');
		$('.prep_hint_pay_receipt').delay(500).queue(function(){
			$('.prep_hint_pay_receipt').remove();
		});
    });
});

//精算　承認者
$(function(){
    var moveElm = $('.hint_pay_authorizer'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_authorizer = '誰の承認によるものか記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 32rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_authorizer">' + hint_pay_authorizer + '</div>').prependTo('body');
            $('.prep_hint_pay_authorizer').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_authorizer').hide('slow');
		$('.prep_hint_pay_authorizer').delay(500).queue(function(){
			$('.prep_hint_pay_authorizer').remove();
		});
    });
});

//精算　JANコード
$(function(){
    var moveElm = $('.hint_pay_jan'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_jan = '物品を購入した場合にはJANコードも記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 41rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_jan">' + hint_pay_jan + '</div>').prependTo('body');
            $('.prep_hint_pay_jan').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_jan').hide('slow');
		$('.prep_hint_pay_jan').delay(500).queue(function(){
			$('.prep_hint_pay_jan').remove();
		});
    });
});

//精算　購入物品名/対象名称
$(function(){
    var moveElm = $('.hint_pay_advance'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_advance = '物品名の固有名を記入してください。<br>懇親会等の場合は飲食費と記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; left: 20%; width: 38rem; height: 7rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_advance">' + hint_pay_advance + '</div>').prependTo('body');
            $('.prep_hint_pay_advance').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_advance').hide('slow');
		$('.prep_hint_pay_advance').delay(500).queue(function(){
			$('.prep_hint_pay_advance').remove();
		});
    });
});

//生産　購入先/支払先
$(function(){
    var moveElm = $('.hint_pay_destination'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_destination = '購入した店舗名を記入してください。<br>飲食費の場合は飲食店の名前を記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 38rem; height: 7rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_destination">' + hint_pay_destination + '</div>').prependTo('body');
            $('.prep_hint_pay_destination').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_destination').hide('slow');
		$('.prep_hint_pay_destination').delay(500).queue(function(){
			$('.prep_hint_pay_destination').remove();
		});
    });
});

//精算　目的（自由入力）
$(function(){
    var moveElm = $('.hint_pay_purpose_free'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_pay_purpose_free = '購入理由に合ったものを記入してください。<br>例）同好会費用<br>例）チーム内懇親会費用';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 34rem; height: 8rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_pay_purpose_free">' + hint_pay_purpose_free + '</div>').prependTo('body');
            $('.prep_hint_pay_purpose_free').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_pay_purpose_free').hide('slow');
		$('.prep_hint_pay_purpose_free').delay(500).queue(function(){
			$('.prep_hint_pay_purpose_free').remove();
		});
    });
});

/*===========================================================
これより　現場情報ページ
===========================================================*/

//現場　現在の現場の要員動向
$(function(){
    var moveElm = $('.hint_site_trend'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_trend = '現在の現場で必要としている人材像を記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 34rem; height: 6rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_trend">' + hint_site_trend + '</div>').prependTo('body');
            $('.prep_hint_site_trend').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_trend').hide('slow');
		$('.prep_hint_site_trend').delay(500).queue(function(){
			$('.prep_hint_site_trend').remove();
		});
    });
});

//現場　顧客名
$(function(){
    var moveElm = $('.hint_site_client_name'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_name = '現在行っている現場の名前を記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 37rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_name">' + hint_site_client_name + '</div>').prependTo('body');
            $('.prep_hint_site_client_name').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_name').hide('slow');
		$('.prep_hint_site_client_name').delay(500).queue(function(){
			$('.prep_hint_site_client_name').remove();
		});
    });
});

//現場　顧客担当者（リーダー）
$(function(){
    var moveElm = $('.hint_site_client_leader'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_leader = '現場の上長の名前を記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 30rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_leader">' + hint_site_client_leader + '</div>').prependTo('body');
            $('.prep_hint_site_client_leader').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_leader').hide('slow');
		$('.prep_hint_site_client_leader').delay(500).queue(function(){
			$('.prep_hint_site_client_leader').remove();
		});
    });
});

//現場　所属会社名
$(function(){
    var moveElm = $('.hint_site_client_affiliated_company_name'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_affiliated_company_name = '未記入で構いません。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 20rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_affiliated_company_name">' + hint_site_client_affiliated_company_name + '</div>').prependTo('body');
            $('.prep_hint_site_client_affiliated_company_name').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_affiliated_company_name').hide('slow');
		$('.prep_hint_site_client_affiliated_company_name').delay(500).queue(function(){
			$('.prep_hint_site_client_affiliated_company_name').remove();
		});
    });
});

//現場　所属担当者（リーダー）
$(function(){
    var moveElm = $('.hint_site_client_affiliated_leader'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_affiliated_leader = '未記入で構いません。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 20rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_affiliated_leader">' + hint_site_client_affiliated_leader + '</div>').prependTo('body');
            $('.prep_hint_site_client_affiliated_leader').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_affiliated_leader').hide('slow');
		$('.prep_hint_site_client_affiliated_leader').delay(500).queue(function(){
			$('.prep_hint_site_client_affiliated_leader').remove();
		});
    });
});

//現場　勤務場所（最寄り駅）
$(function(){
    var moveElm = $('.hint_site_client_work_place'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_work_place = '勤務地と最寄り駅を記入してください。<br>例）南平台（JR渋谷駅）';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 35rem; height: 6rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_work_place">' + hint_site_client_work_place + '</div>').prependTo('body');
            $('.prep_hint_site_client_work_place').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_work_place').hide('slow');
		$('.prep_hint_site_client_work_place').delay(500).queue(function(){
			$('.prep_hint_site_client_work_place').remove();
		});
    });
});

//現場　担当営業
$(function(){
    var moveElm = $('.hint_site_client_sales_staff'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_sales_staff = '自分の営業をしてくれた人を記入してください。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 38rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_sales_staff">' + hint_site_client_sales_staff + '</div>').prependTo('body');
            $('.prep_hint_site_client_sales_staff').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_sales_staff').hide('slow');
		$('.prep_hint_site_client_sales_staff').delay(500).queue(function(){
			$('.prep_hint_site_client_sales_staff').remove();
		});
    });
});

//現場　稼働年月日
$(function(){
    var moveElm = $('.hint_site_client_start_date'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_start_date = '該当する現場に行っていた期間をそれぞれ入力してください。<br>行っている最中の場合は右のカレンダーボックスは未入力でOKです。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 34rem; height: 10rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_start_date">' + hint_site_client_start_date + '</div>').prependTo('body');
            $('.prep_hint_site_client_start_date').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_start_date').hide('slow');
		$('.prep_hint_site_client_start_date').delay(500).queue(function(){
			$('.prep_hint_site_client_start_date').remove();
		});
    });
});

//現場　常駐先名
$(function(){
    var moveElm = $('.hint_site_client_resident_name'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_resident_name = '客先提出用ページで自動表示させるための項目です。<br>必要なければ未入力で構いません。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 34rem; height: 8rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_resident_name">' + hint_site_client_resident_name + '</div>').prependTo('body');
            $('.prep_hint_site_client_resident_name').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_resident_name').hide('slow');
		$('.prep_hint_site_client_resident_name').delay(500).queue(function(){
			$('.prep_hint_site_client_resident_name').remove();
		});
    });
});

//現場　主たる作業場所
$(function(){
    var moveElm = $('.hint_site_client_main_working_space'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_client_main_working_space = '客先提出用ページで自動表示させるための項目です。<br>必要なければ未入力で構いません。';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 30%; right: 20%; width: 34rem; height: 8rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_client_main_working_space">' + hint_site_client_main_working_space + '</div>').prependTo('body');
            $('.prep_hint_site_client_main_working_space').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_client_main_working_space').hide('slow');
		$('.prep_hint_site_client_main_working_space').delay(500).queue(function(){
			$('.prep_hint_site_client_main_working_space').remove();
		});
    });
});

//現場　業務
$(function(){
    var moveElm = $('.hint_site_detail_work_detail'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_work_detail = '大まかな業務内容を記入してください。<br>例）ソーシャルゲームの開発、運用、保守';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 34rem; height: 6rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_work_detail">' + hint_site_detail_work_detail + '</div>').prependTo('body');
            $('.prep_hint_site_detail_work_detail').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_work_detail').hide('slow');
		$('.prep_hint_site_detail_work_detail').delay(500).queue(function(){
			$('.prep_hint_site_detail_work_detail').remove();
		});
    });
});

//現場　プロジェクト名称
$(function(){
    var moveElm = $('.hint_site_detail_project_name'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_project_name = 'プロジェクト名を記入してください。<br>例）新規ゲームアプリ開発<br>例）XRPG開発';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 34rem; height: 8rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_project_name">' + hint_site_detail_project_name + '</div>').prependTo('body');
            $('.prep_hint_site_detail_project_name').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_project_name').hide('slow');
		$('.prep_hint_site_detail_project_name').delay(500).queue(function(){
			$('.prep_hint_site_detail_project_name').remove();
		});
    });
});

//現場　プロジェクト概要
$(function(){
    var moveElm = $('.hint_site_detail_project_outline'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_project_outline = 'プロジェクトの概要を記入してください。プロジェクトという単位ではない場合は自分の作業内容を書いてください。<br>例）ディープラーニングを利用したチャットボット人工知能の開発';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 40rem; height: 12rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_project_outline">' + hint_site_detail_project_outline + '</div>').prependTo('body');
            $('.prep_hint_site_detail_project_outline').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_project_outline').hide('slow');
		$('.prep_hint_site_detail_project_outline').delay(500).queue(function(){
			$('.prep_hint_site_detail_project_outline').remove();
		});
    });
});

//現場　役割・ポジション
$(function(){
    var moveElm = $('.hint_site_detail_role'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_role = '例）ディレクター兼デザイナー　など';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 30rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_role">' + hint_site_detail_role + '</div>').prependTo('body');
            $('.prep_hint_site_detail_role').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_role').hide('slow');
		$('.prep_hint_site_detail_role').delay(500).queue(function(){
			$('.prep_hint_site_detail_role').remove();
		});
    });
});

//現場　全体状況
$(function(){
    var moveElm = $('.hint_site_detail_all_situation'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_all_situation = '現場の雰囲気や進捗状況、チーム構成など';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 32rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_all_situation">' + hint_site_detail_all_situation + '</div>').prependTo('body');
            $('.prep_hint_site_detail_all_situation').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_all_situation').hide('slow');
		$('.prep_hint_site_detail_all_situation').delay(500).queue(function(){
			$('.prep_hint_site_detail_all_situation').remove();
		});
    });
});

//現場　OS
$(function(){
    var moveElm = $('.hint_site_detail_os'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_os = '現場で使用しているPCのOS<br>例）windows8.1pro<br>例）Mac OS 10.13 High Sierra';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 28rem; height: 8rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_os">' + hint_site_detail_os + '</div>').prependTo('body');
            $('.prep_hint_site_detail_os').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_os').hide('slow');
		$('.prep_hint_site_detail_os').delay(500).queue(function(){
			$('.prep_hint_site_detail_os').remove();
		});
    });
});

//現場　webサーバ
$(function(){
    var moveElm = $('.hint_site_detail_server'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_server = '現場で使用しているwebサーバ<br>例）CentOS7.1 Apache<br>例）Ubuntu17.10 Nginx';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 28rem; height: 8rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_server">' + hint_site_detail_server + '</div>').prependTo('body');
            $('.prep_hint_site_detail_server').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_server').hide('slow');
		$('.prep_hint_site_detail_server').delay(500).queue(function(){
			$('.prep_hint_site_detail_server').remove();
		});
    });
});

//現場　開発言語
$(function(){
    var moveElm = $('.hint_site_detail_use_programming_lang'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_use_programming_lang = 'html5.2 css3 php7.1 mysql5.7 python3.6など';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 32rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_use_programming_lang">' + hint_site_detail_use_programming_lang + '</div>').prependTo('body');
            $('.prep_hint_site_detail_use_programming_lang').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_use_programming_lang').hide('slow');
		$('.prep_hint_site_detail_use_programming_lang').delay(500).queue(function(){
			$('.prep_hint_site_detail_use_programming_lang').remove();
		});
    });
});

//現場　DB環境
$(function(){
    var moveElm = $('.hint_site_detail_use_db'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_use_db = '主にプログラムチームですがデータベース周りについて記入してください。<br>例）DBサーバ4台　webサーバ1台　PostgreSQLとSlony pgpoolによるレプリケーションシステムを採用';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 40rem; height: 10rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_use_db">' + hint_site_detail_use_db + '</div>').prependTo('body');
            $('.prep_hint_site_detail_use_db').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_use_db').hide('slow');
		$('.prep_hint_site_detail_use_db').delay(500).queue(function(){
			$('.prep_hint_site_detail_use_db').remove();
		});
    });
});

//現場　フレームワーク
$(function(){
    var moveElm = $('.hint_site_detail_framework'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_framework = '利用しているフレームワークについて記入してください。smartyなどのテンプレートエンジンについてもこちらに記入してください。<br>例）Backbone.js<br>CakePHP3<br>Django<br>Rails<br>Ebaradder-framework（独自フレームワーク）など';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 36rem; height: 18rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_framework">' + hint_site_detail_framework + '</div>').prependTo('body');
            $('.prep_hint_site_detail_framework').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_framework').hide('slow');
		$('.prep_hint_site_detail_framework').delay(500).queue(function(){
			$('.prep_hint_site_detail_framework').remove();
		});
    });
});

//現場　その他ツール
$(function(){
    var moveElm = $('.hint_site_detail_other_tools'), delayTime = 300;
    var moveTimer = 0;

    moveElm.on('mousemove', function(){
        clearTimeout(moveTimer);
        var hint_site_detail_other_tools = 'テキストエディタやFTPソフト、sshソフトなど';
        moveTimer = setTimeout(function(){
            $('<div style="position: absolute; top: 50%; right: 20%; width: 34rem; height: 4rem; z-index: 1; border: 1px solid #000; border-radius: 15px 15px 15px 15px; background-color: rgba(230, 230, 240, 1); display: none; padding: 1rem 2rem;" class="prep_hint_site_detail_other_tools">' + hint_site_detail_other_tools + '</div>').prependTo('body');
            $('.prep_hint_site_detail_other_tools').show('slow');
        }, delayTime);
    }).on('mouseout', function(){
        clearTimeout(moveTimer);
        $('.prep_hint_site_detail_other_tools').hide('slow');
		$('.prep_hint_site_detail_other_tools').delay(500).queue(function(){
			$('.prep_hint_site_detail_other_tools').remove();
		});
    });
});