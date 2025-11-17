"use strict";

const g_isDark = window.matchMedia &&
               window.matchMedia('(prefers-color-scheme: dark)').matches;

let style = newel('style');
style.innerHTML = `
.klawa {
  --side: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  overflow-x: clip;
}
.klawa .pianoside {
  flex: 0 0 var(--side);
}
.klawa .pianoside button {
  width: var(--side);
  aspect-ratio: 1 / 1;
  font-size: calc(var(--side) * 0.78);
  line-height: 1;
  padding: 0;
  box-sizing: border-box;
  display: grid;
  place-items: center;
}
.klawa > svg {
  display: block;
  flex: 0 1 auto;
  min-width: 0;
  width: auto;
  height: auto;
  max-height: min(300px, 100svh);
  max-width: calc(100% - (2 * var(--side)));
  box-sizing: border-box;
}
.klawa.nosides > svg { max-width: 100%; }
.klawa.nosides .pianoside { display: none; }
@media (max-width: 767px) {
	.klawa > svg { max-width: 100%; }
	.klawa .pianoside { display: none; }
}

@keyframes redblink-frames
{
	  0% { box-shadow: 0 0 0 0px   rgba(255, 0, 0, 1); }
	100% { box-shadow: 0 0 0 0.1em rgba(255, 0, 0, 0.2); }
}
.redblink { animation: redblink-frames 1s infinite; }

@media (prefers-color-scheme: dark)
{
	@keyframes redblink-frames
	{
		  0% { box-shadow: 0 0 0 0px   rgba(255, 100, 100, 1); }
		100% { box-shadow: 0 0 0 0.2em rgba(255, 100, 100, 0.2); }
	}	
}

div.modal-background
{
	position: fixed;
	z-index: 1;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
	background: rgba(255, 255, 255, 0.5);
}
div.modal-window
{
	width: max-content;
	max-width: 85%;
	margin: 0 auto;
	padding: 1em;
	border: thick solid gray;
	border-radius: 1em;
	max-height: 85vh;
	overflow: auto;
	background: white;
	color: black;
	text-align: left;
}
@media (prefers-color-scheme: dark) {
div.modal-window
{
	background: #111;
	color: #eee;
}}
div.modal-window fieldset label { display: inline-block; }

.whitekeybutton.justpressed svg.path_container path:nth-of-type(1)
{ animation: fadeOut 2s forwards; }
@keyframes fadeOut
{ from { fill: #a6ded8; fill-opacity: 1; } to   { fill: #a6ded8; fill-opacity: 0; } }
.blackkeybutton.justpressed path { animation: blackKeyJustPressed 2s forwards; }
@keyframes blackKeyJustPressed { from { fill: #099e99; } to { fill: black; } }

.ostylowany
{
	display: inline-block;
	font-size: 1rem;
	line-height: 1;
	background: #eeeeee;
	color: black;
	font-family: Arial;
	margin: 0.25rem;
	padding: 4px;
	border: 1px solid gray;
	border-radius: 0.5em;
}
.ostylowany:hover { background: #f7f7f7; border: 1px solid black; }

@media (prefers-color-scheme: dark)
{
	.ostylowany { color: #eeeeee; background: #555; }
	.ostylowany:hover { background: #000; border: 1px solid white; }
}
.ostylowany input, .ostylowany select { margin: 0 0 0 0.5em; }
.ostylowany button { margin: 0; vertical-align: middle; }
.ostylowany select {  }
 
@media (max-width: 767px) { .niemagdyciasno { display: none; } }

`;
document.head.append(style);

"use strict";

function words_Grand_Piano_preloaded()
{
	if (g_lang === 'es') return 'Piano de cola (precargado)';
	if (g_lang === 'ru') return 'Рояль (быстрая загрузка)';
	return 'Grand Piano (preloaded)';
}
function words_Grand_Piano_bettersound()
{
	if (g_lang === 'es') return 'Piano de cola (alta calidad)';
	if (g_lang === 'ru') return 'Рояль (улучшенное качество звука)';
	return 'Grand Piano (better sound)';
}
function words_Smooth_Electric_Piano()
{
	if (g_lang === 'es') return 'Piano eléctrico (suave)';
	if (g_lang === 'ru') return 'Электропианино (мягкое)';
	return 'Electric Piano (smooth)';
}
function words_Soft_Electric_Piano()
{
	if (g_lang === 'es') return 'Piano eléctrico (apagado)';
	if (g_lang === 'ru') return 'Электропианино (приглушённое)';
	return 'Electric Piano (gentle)';
}
function words_Digital_Piano_bells()
{
	if (g_lang === 'es') return 'Piano electrónico (campanillas)';
	if (g_lang === 'ru') return 'Электронное пианино (колокольчики)';
	return 'Chimes Piano';
}
function words_Classical_Guitar()
{
	if (g_lang === 'es') return 'Guitarra clásica';
	if (g_lang === 'ru') return 'Классическая гитара';
	return 'Classical Guitar';
}
function words_Tab_plays()
{
	if (g_lang === 'es') return 'Tab toca';
	if (g_lang === 'ru') return 'Tab играет';
	return 'Tab plays';
}
function words_Voice()
{
	if (g_lang === 'es') return 'Sonido';
	if (g_lang === 'ru') return 'Инструмент';
	return 'Voice';
}
function words_volume_down()
{
	if (g_lang === 'es') return 'Bajar volumen';
	if (g_lang === 'ru') return 'Уменьшить громкость';
	return 'volume down';
}
function words_volume_up()
{
	if (g_lang === 'es') return 'Subir volumen';
	if (g_lang === 'ru') return 'Увеличить громкость';
	return 'volume up';
}
function words_Volume()
{
	if (g_lang === 'es') return 'Volumen ';
	if (g_lang === 'ru') return 'Громкость ';
	return 'Volume ';
}
function words_Sustain()
{
	if (g_lang === 'ru') return 'Сустейн';
	return 'Sustain';
}
function words_clone_this_piano()
{
	if (g_lang === 'es') return 'Duplicar este piano';
	if (g_lang === 'ru') return 'Клонировать это пианино';
	return 'clone this piano';
}
function words_remove_this_piano()
{
	if (g_lang === 'es') return 'Eliminar este piano';
	if (g_lang === 'ru') return 'Удалить это пианино';
	return 'remove this piano';
}
function words_Remove_this_piano()
{
	if (g_lang === 'es') return '¿Eliminar este piano?';
	if (g_lang === 'ru') return 'Удалить это пианино?';
	return 'Remove this piano?';
}
function words_Delete()
{
	if (g_lang === 'es') return 'Eliminar';
	if (g_lang === 'ru') return 'Удалить';
	return 'Delete';
}
function words_C()
{
	if (g_lang === 'es') return 'Do';
	return 'C';
}
function words_Note_names()
{
	if (g_lang === 'es') return 'Notación';
	if (g_lang === 'ru') return 'Обозначения нот';
	return 'Note names';
}
function words_Download_image()
{
	if (g_lang === 'es') return 'Descargar imagen';
	if (g_lang === 'ru') return 'Скачать изображение';
	return 'Download image';
}
function words_Pixel_height()
{
	if (g_lang === 'es') return 'alto';
	if (g_lang === 'ru') return 'высота';
	return 'height';
}
function words_Image_type()
{
	if (g_lang === 'es') return 'formato';
	if (g_lang === 'ru') return 'формат';
	return 'format';
}
function words_Cancel()
{
	if (g_lang === 'es') return 'Cancelar';
	if (g_lang === 'ru') return 'Отмена';
	return 'Cancel';
}
function words_Label()
{
	if (g_lang === 'es') return 'Etiqueta';
	if (g_lang === 'ru') return 'Метка';
	return 'Label';
}
function words_Save()
{
	if (g_lang === 'es') return 'Guardar';
	if (g_lang === 'ru') return 'Сохранить';
	return 'Save';
}
function words_AssignedKey()
{
	if (g_lang === 'es') return 'Tecla del teclado de la computadora asignada';
	if (g_lang === 'ru') return 'Назначенная клавиша на клавиатуре компьютера';
	return 'Assigned computer keyboard key';
}
function words_ButtonSettings()
{
	if (g_lang === 'es') return 'Editar botón';
	if (g_lang === 'ru') return 'Настройки кнопки';
	return 'Button settings';
}
function words_ChordPlaybackSettings()
{
	if (g_lang === 'es') return 'Configuración de reproducción del acorde';
	if (g_lang === 'ru') return 'Настройки воспроизведения аккорда';
	return 'Chord playback settings';
}
function words_Playnotes()
{
	if (g_lang === 'es') return 'Tocar las notas';
	if (g_lang === 'ru') return 'Воспроизвести ноты';
	return 'Play notes';
}
function words_allatonce()
{
	if (g_lang === 'es') return 'todas a la vez';
	if (g_lang === 'ru') return 'одновременно';
	return 'all at once';
}
function words_inAscendingOrder()
{
	if (g_lang === 'es') return 'en orden ascendente';
	if (g_lang === 'ru') return 'по возрастанию';
	return 'in ascending order';
}
function words_inDescendingOrder()
{
	if (g_lang === 'es') return 'en orden descendente';
	if (g_lang === 'ru') return 'по убыванию';
	return 'in descending order';
}
function words_DurationFromFirst2LastNote()
{
	if (g_lang === 'es') return 'Duración desde la primera hasta la última nota:';
	if (g_lang === 'ru') return 'Длительность от первой до последней ноты:';
	return 'Duration from first to last note:';
}
function words_ms()
{
	if (g_lang === 'ru') return 'мс';
	return 'ms';
}
function words_Duplicate()
{
	if (g_lang === 'es') return 'Duplicar';
	if (g_lang === 'ru') return 'Клонировать';
	return 'Duplicate';
}
function words_ChordButtonEditor()
{
	if (g_lang === 'es') return 'Editor del botón de acorde';
	if (g_lang === 'ru') return 'Редактор кнопки аккорда';
	return 'Chord Button Editor';
}
function words_CHORD()
{
	if (g_lang === 'es') return 'ACORDE';
	if (g_lang === 'ru') return 'АККОРД';
	return 'CHORD';
}
function words_RECORD()
{
	if (g_lang === 'es') return 'GRABAR';
	if (g_lang === 'ru') return 'ЗАПИСЬ';
	return 'RECORD';
}
function words_PlaybackButtonEditor()
{
	if (g_lang === 'es') return 'Editor del botón de reproducción';
	if (g_lang === 'ru') return 'Редактор кнопки воспроизведения';
	return 'Playback Button Editor';
}
function words_Download()
{
	if (g_lang === 'es') return 'Descargar';
	if (g_lang === 'ru') return 'Скачать';
	return 'Download';
}
function words_a_simplified_MIDI_file()
{
	if (g_lang === 'es') return 'un archivo MIDI simplificado';
	if (g_lang === 'ru') return 'упрощённый MIDI-файл';
	return 'a simplified MIDI file';
}
function words_SaveDownload()
{
	if (g_lang === 'es') return 'Exportar';
	if (g_lang === 'ru') return 'Экспорт';
	return 'Export';
}
function words_SaveDownloadTooltip()
{
	if (g_lang === 'es') return 'Exporta y descarga un archivo para guardar tu proyecto';
	if (g_lang === 'ru') return 'Экспорт и загрузка файла для сохранения проекта';
	return 'Downloads a file to save your project';
}
function words_LoadApronus()
{
	if (g_lang === 'es') return 'Cargar proyecto de piano de Apronus';
	if (g_lang === 'ru') return 'Загрузить проект пианино с Apronus';
	return 'Load Apronus Piano Project';
}
function words_range()
{
	if (g_lang === 'es') return 'Rango';
	if (g_lang === 'ru') return 'Диапазон';
	return 'Range';
}
function words_MoreFeatures()
{
	if (g_lang === 'es') return 'Más funciones';
	if (g_lang === 'ru') return 'Больше функций';
	return 'More Features';
}
function words_Close()
{
	if (g_lang === 'es') return 'Cerrar';
	if (g_lang === 'ru') return 'Закрыть';
	return 'Close';
}
function words_bookmark_yourchords()
{
	if (g_lang === 'es') return 'Enlace a tus acordes';
	if (g_lang === 'ru') return 'Ссылка на ваши аккорды';
	return 'Link to your chords';
}
function words_bookmark_explained()
{
	if (g_lang === 'es') return 'Este enlace abre el piano virtual en una nueva pestaña con tus acordes en la dirección web. Puedes guardarlo como marcador o compartirlo para acceder a esos mismos acordes más tarde';
	if (g_lang === 'ru') return 'Эта ссылка откроет виртуальное пианино в новой вкладке с вашими аккордами в адресе. Вы можете сохранить её в закладки или поделиться ею, чтобы позже снова открыть те же аккорды.';
	return 'This link opens the virtual piano in a new tab with your chords in the web address. You can save it as a bookmark or share it to access the same chords later.';
}
function words_Layout()
{
	if (g_lang === 'es') return 'Distribución';
	if (g_lang === 'ru') return 'Раскладка';
	return 'Layout';
}
function words_DefaultLayout()
{
	if (g_lang === 'es') return 'Teclas de piano';
	if (g_lang === 'ru') return 'Клавиши пианино';
	return 'Piano Keys';
}
function words_WhiteKeys()
{
	if (g_lang === 'es') return 'Teclas blancas';
	if (g_lang === 'ru') return 'Белые клавиши';
	return 'White Keys';
}
function words_AdjustRange()
{
	if (g_lang === 'es') return 'Ajustar el rango';
	if (g_lang === 'ru') return 'Настроить диапазон';
	return 'Adjust the range';
}
function words_addlower()
{
	if (g_lang === 'es') return "Añadir octava inferior";
	if (g_lang === 'ru') return 'Добавить нижнюю октаву';
	return 'Add lower octave';
}
function words_addhigher()
{
	if (g_lang === 'es') return "Añadir octava superior";
	if (g_lang === 'ru') return 'Добавить верхнюю октаву';
	return 'Add higher octave';
}
function words_removelowest()
{
	if (g_lang === 'es') return "Quitar la octava más baja";
	if (g_lang === 'ru') return 'Убрать самую нижнюю октаву';
	return 'Remove lowest octave';
}
function words_removehighest()
{
	if (g_lang === 'es') return "Quitar la octava más alta";
	if (g_lang === 'ru') return 'Убрать самую верхнюю октаву';
	return 'Remove highest octave';
}
function words_ManageMultiple()
{
	if (g_lang === 'es') return 'Gestionar varios pianos';
	if (g_lang === 'ru') return 'Управление несколькими пианино';
	return 'Manage multiple pianos';
}
function words_Loop_endlessly()
{
	if (g_lang === 'es') return 'Repetir indefinidamente';
	if (g_lang === 'ru') return 'Повторять бесконечно';
	return 'Loop endlessly';
}
function words_Silence()
{
	if (g_lang === 'es') return 'Silencio';
	if (g_lang === 'ru') return 'Тишина';
	return 'Silence';
}
function words_StopsAll()
{
	if (g_lang === 'es') return 'Detiene de inmediato toda la reproducción de todos los pianos.';
	if (g_lang === 'ru') return 'Мгновенно останавливает всё воспроизведение на всех пианино.';
	return 'Immediately stops all playback from every piano.';
}
function words_NewMethod()
{
	if (g_lang === 'es') return 'Método nuevo';
	if (g_lang === 'ru') return 'Новый метод';
	return 'New method';
}
function words_OldMethod()
{
	if (g_lang === 'es') return 'Método antiguo';
	if (g_lang === 'ru') return 'Старый метод';
	return 'Old method';
}
function words_YouCan()
{
	if (g_lang === 'es') return 'Puedes ';
	if (g_lang === 'ru') return 'Вы можете ';
	return 'You can ';
}
function words_RemoveAds()
{
	if (g_lang === 'es') return 'quitar los anuncios';
	if (g_lang === 'ru') return 'убрать рекламу';
	return 'remove ads';
}
function words_but()
{
	if (g_lang === 'es') return ' pero ';
	if (g_lang === 'ru') return ', но ';
	return ' but ';
}
function words_PleaseDonate()
{
	if (g_lang === 'es') return 'por favor dona';
	if (g_lang === 'ru') return 'пожалуйста, пожертвуйте';
	return 'please donate';
}
function words_regularly()
{
	if (g_lang === 'es') return ' regularmente.';
	if (g_lang === 'ru') return ' регулярно.';
	return ' regularly.';
}
function words_Import()
{
	if (g_lang === 'es') return 'Importar';
	if (g_lang === 'ru') return 'Импорт';
	return 'Import';
}
function MarkKeys()
{
	if (g_lang === 'es') return 'Marcar teclas';
	if (g_lang === 'ru') return 'Отметить клавиши';
	return 'Mark keys';
}
function MarkKeysTip()
{
	if (g_lang === 'es') return 'Usa este interruptor para resaltar las teclas seleccionadas, por ejemplo, para indicar una escala.';
	if (g_lang === 'ru') return 'Используйте этот переключатель, чтобы подсветить выбранные клавиши, например, чтобы показать гамму.';
	return 'Use this toggle to highlight selected keys, for example to indicate a scale.';
}
function words_FlipLayout()
{
	if (g_lang === 'es') return 'Cambiar vista';
	if (g_lang === 'ru') return 'Поменять вид';
	return 'Flip layout';
}
function words_FlipLayout_Info()
{
	if (g_lang === 'es') return 'Cambia el diseño para que los controles estén debajo del teclado.';
	if (g_lang === 'ru') return 'Меняет расположение: элементы управления будут под клавиатурой.';
	return 'Places the controls below the piano keyboard.';
}
function words_Fullscreen()
{
	if (g_lang === 'es') return 'Pantalla completa';
	if (g_lang === 'ru') return 'Полноэкранный режим';
	return 'Fullscreen';
}
function words_EnterFullscreen()
{
	if (g_lang === 'es') return 'Entrar en pantalla completa';
	if (g_lang === 'ru') return 'Войти в полноэкранный режим';
	return 'Enter fullscreen';
}
function words_ExitFullscreen()
{
	if (g_lang === 'ru') return 'Выйти из полноэкранного режима';
	if (g_lang === 'es') return 'Salir de pantalla completa';
	return 'Exit fullscreen';
}
"use strict";

let audiocontext = null;

function resetAudioContext()
{
  if (audiocontext)
  {
    audiocontext.close().finally(() => { audiocontext = new AudioContext(); });
  }
  else
  {
    audiocontext = new AudioContext();
  }
}

resetAudioContext();

function load_sounds(buffers, sounds, URL)
{
	function load(n)
	{
		if (buffers[n] !== null) return; // already loaded
		let url = URL(n);
		var request = new XMLHttpRequest();
		request.open("GET", url, true);
		request.responseType = "arraybuffer";
		request.onload = function()
		{
			audiocontext.decodeAudioData
			(
			  request.response,
			  function(buffer) { buffers[n] = buffer; },
			  function(error) { }
			);
		}
		request.onerror = function() { }
		request.send();
	}
	for (let n of sounds) load(n);
}

function buffer_source_start(buffer, rate, volume, sustain, delayInSeconds)
{
	if (audiocontext.state === 'suspended') audiocontext.resume();
	var gainNode = audiocontext.createGain();
	gainNode.gain.value = volume;

	if (!sustain)
	{
	 var duration = 0.6; // in seconds
	 var start = audiocontext.currentTime + delayInSeconds;
	 var stop = start + duration;
	 gainNode.gain.setValueAtTime(volume, start);
	 gainNode.gain.linearRampToValueAtTime(volume, stop);
	 gainNode.gain.linearRampToValueAtTime(0, stop+0.1);
	}

	var source = audiocontext.createBufferSource();
	source.buffer = buffer;
	source.playbackRate.value = rate;
	source.connect(gainNode);
	gainNode.connect(audiocontext.destination);
	source.start(audiocontext.currentTime + delayInSeconds);
}

function voice_piano7()
{
	const piano7sounds = [];
	for (let i=24; i<=108; i++) piano7sounds[i] = null;

	function load()
	{
		function URL(n)
		{
			const prefix = "/static/piano7sounds/";
			let octave = Math.floor(n/12)-1;
			let names = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B','Cb'];
			let name = names[n%12];
			return prefix + name + octave + '.mp3';
		}
		let sounds = [51, 58, 65, 72, 79, 44, 37, 86, 93, 30, 24];
		load_sounds(piano7sounds, sounds, URL);
	}

	function play(n, delayInSeconds, volume, sustain)
	{
		function adopt(src)
		{
			let buffer = piano7sounds[src];
			let rate = Math.pow(2,(n-src)/12);
			buffer_source_start(buffer, rate, volume, sustain, delayInSeconds);
		}
		if (n <= 28) return adopt(24);
		if (n >= 93) return adopt(93);
		if (n%7 == 2) return adopt(n);
		if (n%7 == 3) return adopt(n-1);
		if (n%7 == 4) return adopt(n-2);
		if (n%7 == 5) return adopt(n-3);
		if (n%7 == 6) return adopt(n+3);
		if (n%7 == 0) return adopt(n+2);
		if (n%7 == 1) return adopt(n+1);
	}

	return {
		id: 'piano7',
		name: words_Grand_Piano_preloaded(),
		load: load,
		play: play,
		volumeFactor: 1
	};
}

function voice_pno0x3()
{
	const pno0x3_pianosounds = [];
	for (let i=24; i<=108; i++) pno0x3_pianosounds[i] = null;

	function load()
	{
		let sounds = [];
		for (let n = 30; n <= 96; n = n+3) sounds.push(n);
		function URL(n) { return '/static/pno0x3-pianosounds/pno0'+n+'.mp3'; }
		load_sounds(pno0x3_pianosounds, sounds, URL);
	}
	
	function play(n, delayInSeconds, volume, sustain)
	{
		function volumeFactor()
		{
			switch (n)
			{
				case 56: case 57: case 58: return 0.6;
				case 62: case 63: case 64: return 0.7;
				case 65: case 66: case 67: return 0.5;
				case 68: case 69: case 69: return 0.7;
			}
			if (n<=38) return 0.4;
			if (n<=45) return 0.6;
			return 1;
		}

		function adopt(src)
		{
			let buffer = pno0x3_pianosounds[src];
			let rate = Math.pow(2,(n-src)/12);
			volume = volume * volumeFactor();
			buffer_source_start(buffer, rate, volume, sustain, delayInSeconds);
		}
		
			  if (n==38) adopt(36);
		 else if (n==39) { adopt(42); adopt(36); }
		 else if (n==40) adopt(42);
		 else if (n>=96) adopt(96);
		 else if (n<30) adopt(30);
		 else switch (n%3)
		 {
		  case 0 : adopt(n); break;
		  case 1 : adopt(n-1); break;
		  case 2 : adopt(n+1);
		 }
	}

	return {
		id: 'pno0x3',
		name: words_Grand_Piano_bettersound(),
		load: load,
		play: play,
		volumeFactor: 1
	}
}

function voice_crocodile()
{
	const CrocodilePiano_sounds = [];
	for (let i=24; i<=108; i++) CrocodilePiano_sounds[i] = null;

	function load()
	{
		function URL(n) { return "/static/CrocodilePiano/" + n + '.wav'; };
		let sounds = [38, 43, 50, 55, 62, 67, 74, 28, 33];
		load_sounds(CrocodilePiano_sounds, sounds, URL);
	}

	function play(n, delayInSeconds, volume, sustain)
	{
		function adopt(src, quieter)
		{
			let buffer = CrocodilePiano_sounds[src];
			let rate = Math.pow(2,(n-src)/12);
			volume = volume * quieter;
			buffer_source_start(buffer, rate, volume, sustain, delayInSeconds);
		}
		if (n <= 30) return adopt(28, 0.7);
		if (n <= 35) return adopt(33, 0.7);
		if (n <= 40) return adopt(38, 0.7);
		if (n <= 46) return adopt(43, 0.7);
		if (n <= 52) return adopt(50, 0.5);
		if (n <= 58) return adopt(55, 0.5);
		if (n <= 64) return adopt(62, 0.5);
		if (n <= 70) return adopt(67, 0.4);
		if (n >= 71) return adopt(74, 0.5);
	}
	
	return {
		id: 'crocodile',
		name: words_Smooth_Electric_Piano(),
		load: load,
		play: play,
		volumeFactor: 0.5
	}
}

function voice_soft_sus_pleck_slim()
{
	const buffers = [];
	for (let i=24; i<=108; i++) buffers[i] = null;
	
	function load()
	{
		function URL(n) { return '/static/soft_sus_pleck_slim/' + n + '.mp3'; };
		let sounds = [44, 51, 58, 65, 72, 79];
		load_sounds(buffers, sounds, URL);
	}
	function play(n, delayInSeconds, volume, sustain)
	{
		function adopt(src)
		{
			let buffer = buffers[src];
			let rate = Math.pow(2,(n-src)/12);
			if (src <= 51) volume *= 2;
			buffer_source_start(buffer, rate, volume, sustain, delayInSeconds);
		}
		if (n <= 44) return adopt(44);
		if (n >= 79) return adopt(79);
		if (n%7 === 2) return adopt(n);
		if (n%7 === 3) return adopt(n-1);
		if (n%7 === 4) return adopt(n-2);
		if (n%7 === 5) return adopt(n-3);
		if (n%7 === 6) return adopt(n+3);
		if (n%7 === 0) return adopt(n+2);
		if (n%7 === 1) return adopt(n+1);
	}
	
	return {
		id: 'soft_sus_pleck_slim',
		name: words_Classical_Guitar(),
		load: load,
		play: play,
		volumeFactor: 2
	}
}

function voice_e2e3e4()
{
	const buffers = [];
	for (let i=24; i<=108; i++) buffers[i] = null;
	
	function load()
	{
		function URL(n) { return '/static/e2e3e4/' + n + '.wav'; };
		let sounds = [40, 52, 64];
		load_sounds(buffers, sounds, URL);
	}
	function play(n, delayInSeconds, volume, sustain)
	{
		function adopt(src, v)
		{
			let buffer = buffers[src];
			let rate = Math.pow(2,(n-src)/12);
			let vol = volume * v;
			if (src === 64 && rate <= 2) vol *= 0.7;
			buffer_source_start(buffer, rate, vol, sustain, delayInSeconds);
		}
		if (n <= 40) return adopt(40, 1);
		if (n >= 64) return adopt(64, 0.6);
		if (n <= 52)
		{
			let y = (n - 40)/12;
			adopt(52, y);
			adopt(40, 1-y);
			return;
		}
		let y = (n - 52)/12;
		adopt(52, 1-y);
		adopt(64, y);
	}
	
	return {
		id: 'e2e3e4',
		name: words_Digital_Piano_bells(),
		load: load,
		play: play,
		volumeFactor: 0.3
	}
}

function voice_samurai()
{
	const buffers = [];
	for (let i=24; i<=108; i++) buffers[i] = null;
	
	function load()
	{
		function URL(n) { return '/static/samurai/' + n + '.wav'; };
		let sounds = [40, 70];
		load_sounds(buffers, sounds, URL);
	}
	function play(n, delayInSeconds, volume, sustain)
	{
		function adopt(src, v)
		{
			let buffer = buffers[src];
			let rate = Math.pow(2,(n-src)/12);
			let vol = volume * v;
			if (n >= 67) vol *= 0.7;
			buffer_source_start(buffer, rate, vol, sustain, delayInSeconds);
		}
		if (n <= 51) return adopt(40, 1);
		if (n <= 70)
		{
			let v = (70-n)/(70-52);
			adopt(40, v);
			adopt (70, 1-v);
			return;
		}
		adopt(70, 1);
	}
	
	return {
		id: 'samurai',
		name: words_Soft_Electric_Piano(),
		load: load,
		play: play,
		volumeFactor: 0.3
	}
}

const g_voices =
	[
		voice_piano7(),
		voice_pno0x3(),
		voice_crocodile(),
		voice_samurai(),
		voice_e2e3e4(),
		voice_soft_sus_pleck_slim()
	]; // this does not load sounds

function getVoiceById(id)
{
	for (let voice of g_voices) if (voice.id === id) return voice;
	return g_voices[0];
}
"use strict";

const TWO_OCTAVES_DEFAULT =
{
	 'Backquote': [],
		'Tab':    [1],
		'Digit1': [2],
		'KeyQ':   [3],
		'Digit2': [4],
		'KeyW':   [5],
		'Digit3': [],
		'KeyE':   [6],
		'Digit4': [7],
		'KeyR':   [8],
		'Digit5': [9],
		'KeyT':   [10],
		'Digit6': [11],
		'KeyY':   [12],
		'Digit7': [],
		'KeyU':   [13],
		'Digit8': [14],
		'KeyI':   [15],
		'Digit9': [16],
		'KeyO':   [17],
		'Digit0': [],
		'KeyP':   [18],
		'Minus':  [19],
	'BracketLeft':[20],
		'Equal':  [21],
  'BracketRight': [22],
	'Backspace':  [23],
	'Backslash':  [24],
		'Enter':  [25]
};
const LOWER_SINGLE_NOTES_CCG =
{
	'CapsLock': [],
	'ShiftLeft': [-11],
	'IntlBackslash': [-11],
	'KeyA': [-10],
	'KeyZ': [-9],
	'KeyS': [-8],
	'KeyX': [-7],
	'KeyD': [],
	'KeyC': [-6],
	'KeyF': [-5],
	'KeyV': [-4],
	'KeyG': [-3],
	'KeyB': [-2],
	'KeyH': [-1],
	'KeyN': [0],
	'KeyJ': [],
	'KeyM': [1],
	'KeyK': [2],
	'Comma': [3],
	'KeyL': [4],
	'Period': [5],
	'Semicolon': [],
	'Slash': [6],
	'Quote': [7],
	'ShiftRight': [8]
}

const EXTENDED_DEFAULT =
{
	  'Numpad7': [25],
	  'NumLock': [],
 'NumpadDivide': [26],
	  'Numpad8': [27],
'NumpadMultiply':[28],
	  'Numpad9': [29],
	'NumpadAdd': [30],
'NumpadSubtract':[31],
   'NumpadEnter':[32]
};

const EXTENDED_NAVIGATION =
{
	'Insert': [],
	'Delete': [25],
	'Home': [26],
	'End': [27],
	'PageUp': [28],
	'PageDown': [29],
	'Numpad7': [30],
	'NumpadDivide': [31],
	'Numpad8': [32],
	'NumpadMultiply': [33],
	'Numpad9': [34],
	'NumpadSubtract': [35],
	'NumpadAdd':[36],
	'NumpadEnter':[37]
}

const LOWER_NUMPAD =
{
'Enter': [9],
'Numpad1': [10],
'Numpad4': [11],
'Numpad2': [12],
'Numpad3': [13],
'Numpad5': [],
'Numpad6': [14],
'NumpadDecimal': [15]
}

const DEFAULT_LAYOUT = {...TWO_OCTAVES_DEFAULT, ...EXTENDED_DEFAULT, ...LOWER_SINGLE_NOTES_CCG};
const NAVIGATION_LAYOUT = {...TWO_OCTAVES_DEFAULT, ...EXTENDED_NAVIGATION, ...LOWER_SINGLE_NOTES_CCG, LOWER_NUMPAD};

let g_mini_mode_on = true;

function layout_default_minimal(e)
{
	if (e.code.indexOf('Key') === 0) g_mini_mode_on = false;
	else if (g_mini_mode_on) return null;

	if (e.ctrlKey || e.altKey) return null;
		
	if (e.code === 'CapsLock') return { action: 'sustain' };
	
	const layout = {...TWO_OCTAVES_DEFAULT, ...LOWER_SINGLE_NOTES_CCG};
	
	let arr = layout[e.code];
	if (arr) return { offsets: arr.map(x => x-1) }; // may return { offsets: [] }
	
	return null;
}

let g_navigation_present = false;
let g_numpad_present = false;

function layout_default_maximal(e)
{
	let minimal = layout_default_minimal(e);
	if (minimal !== null) return minimal;
	
	if (e.ctrlKey || e.altKey) return null;
	
	if (g_mini_mode_on) return null;

	if (e.code === 'F5') return { action: 'none' };
	if (e.code === 'Space') return { action: 'record' };
	if (e.code === 'ArrowLeft') return { action: 'octave_down' };
	if (e.code === 'ArrowRight') return { action: 'octave_up' };
	if (e.code === 'ArrowDown') return { action: 'voice_down' };
	if (e.code === 'ArrowUp') return { action: 'voice_up' };
	
	function which_layout()
	{
		const navigation = ['Insert', 'Home', 'PageUp', 'Delete', 'End', 'PageDown'];
		if (navigation.includes(e.code)) g_navigation_present = true;
		if (e.location === 3) g_numpad_present = true;

		if (g_navigation_present) return NAVIGATION_LAYOUT;
		if (g_numpad_present) return {...DEFAULT_LAYOUT, ...LOWER_NUMPAD};
		return DEFAULT_LAYOUT;
	}
	let layout = which_layout();
	let arr = layout[e.code];
	
	if (arr) return { offsets: arr.map(x => x-1) }; // may return { offsets: [] }
	
	return null;
}

const ShiftRow = [ 'ShiftLeft', 'KeyZ', 'KeyX', 'KeyC', 'KeyV', 'KeyB', 'KeyN', 'KeyM', 'Comma', 'Period', 'Slash', 'ShiftRight' ];
const CapsRow = [ 'CapsLock', 'KeyA', 'KeyS', 'KeyD', 'KeyF', 'KeyG', 'KeyH', 'KeyJ', 'KeyK', 'KeyL', 'Semicolon', 'Quote', 'Enter' ];
const TabRow = [ 'Tab', 'KeyQ', 'KeyW', 'KeyE', 'KeyR', 'KeyT', 'KeyY', 'KeyU', 'KeyI', 'KeyO', 'KeyP', 'BracketLeft', 'BracketRight', 'Backslash' ];
const DigitsRow = [ 'Backquote', 'Digit1', 'Digit2', 'Digit3', 'Digit4', 'Digit5', 'Digit6', 'Digit7', 'Digit8', 'Digit9', 'Digit0', 'Minus', 'Equal', 'Backspace' ];

const majorScale = [ 2, 2, 1, 2, 2, 2, 1 ];

function ScaleLayout({ tab_offset, scale, tab_step })
{
	function increment(step) // how many semitones from the previous step 
	{
		function mod(a, b) { return ((a % b) + b) % b; }
		return scale[mod(step-2, scale.length)];
	}
	
	let layout = { 'Tab' : [ tab_offset ] };
	for (let i = 1; i < TabRow.length; i++)
	{
		let step = tab_step + i;
		let [ prevOffset ] = layout[TabRow[i-1]];
		let offset = prevOffset + increment(step);
		let key = TabRow[i];
		layout[key] = [ offset ];
	}
	layout['CapsLock'] = [ layout['Tab'][0] - increment(tab_step) ];
	for (let i = 1; i < CapsRow.length; i++)
	{
		let step = tab_step - 1 + i;
		let [ prevOffset ] = layout[CapsRow[i-1]];
		let offset = prevOffset + increment(step);
		let key = CapsRow[i];
		layout[key] = [ offset ];
	}
	layout['ShiftLeft'] = [ layout['CapsLock'][0] -increment(tab_step - 1) ];
	for (let i = 1; i < ShiftRow.length; i++)
	{
		let step = tab_step - 2 + i;
		let [ prevOffset ] = layout[ShiftRow[i-1]];
		let offset = prevOffset + increment(step);
		let key = ShiftRow[i];
		layout[key] = [ offset ];
	}
	layout['Backquote'] = [ tab_offset + increment(tab_step + 1) + increment(tab_step + 2) ];
	for (let i = 1; i < DigitsRow.length; i++)
	{
		let step = tab_step + 2 + i;
		let [ prevOffset ] = layout[DigitsRow[i-1]];
		let offset = prevOffset + increment(step);
		let key = DigitsRow[i];
		layout[key] = [ offset ];
	}
	layout['IntlBackslash'] = layout['ShiftLeft'];
	return layout;
}

function increment(scale, step) // how many semitones from the previous step 
{
	function mod(a, b) { return ((a % b) + b) % b; }
	return scale[mod(step-2, scale.length)];
}

function major_scale_layout(root, tab_step)
{
	const roots = ["C", "D♭", "D", "E♭", "E", "F", "F♯", "G", "A♭", "A", "B♭", "B"];
	const modes = [ "Ionian", "Dorian", "Phrygian", "Lydian", "Mixolydian", "Aeolian", "Locrian" ];
	
	let inc = 0;
	for (let i = 2; i <= tab_step; i++) inc += increment(majorScale, i);
	let tab_offset = (root + inc) % 12;
	if (tab_offset > 7) tab_offset -= 12;
	
	let layout_dictionary = ScaleLayout({ tab_offset: tab_offset, scale: majorScale, tab_step: tab_step });
	function layout_function(e)
	{
		if (e.ctrlKey) return null;
		let arr = layout_dictionary[e.code];
		if (arr)
		{
			if (e.altKey) return { offsets: arr.map(x => x+1) };
			//if (e.ctrlKey) return { offsets: arr.map(x => x-1) };
			return { offsets: arr };
		}
		if (e.code === 'F5') return { action: 'none' };
		if (e.code === 'Space') return { action: 'record' };
		if (e.code === 'ArrowLeft') return { action: 'octave_down' };
		if (e.code === 'ArrowRight') return { action: 'octave_up' };
		if (e.code === 'ArrowDown') return { action: 'voice_down' };
		if (e.code === 'ArrowUp') return { action: 'voice_up' };
		return null;		
	}
	function offscale_notes()
	{
		if (root === 0) return [];
		let scale = [0, 2, 4, 5, 7, 9, 11];
		let ret = [];
		for (let n = 24; n <= 108; n++)
			if (!scale.includes((n - root)%12)) ret.push(n);
		return ret;
	}
	return {
		id: 'major_scale_' + root + tab_step,
		function: layout_function,
		tabPitchClass: tab_offset,
		name: roots[root] + ' Major Scale - ' + modes[tab_step-1] + ' (Mode ' + tab_step + ')',
		offscale: offscale_notes()
	};
}

const default_white_keys_layout = major_scale_layout(0, 1);

let g_layouts = [
	{
		id: 'default_maximal',
		function: layout_default_maximal,
		tabPitchClass: 0,
		name: words_DefaultLayout()
	},
	{
		id: 'whitekeys',
		function: default_white_keys_layout.function,
		tabPitchClass: default_white_keys_layout.tabPitchClass,
		name: words_WhiteKeys()
	}
];

function  major_scale_arpeggion_layouts()
{
	let layouts = [];
	for (let root = 0; root < 12; root++) for (let mode = 1; mode < 8; mode++)
		layouts.push(major_scale_layout(root, mode));
	return layouts;
}

function keydown_function(press, panel, layout)
{
	return function keydown(e)
	{
		if (document.activeElement.nodeName === 'TEXTAREA') return;
		if (document.activeElement.nodeName === 'INPUT')
		{
			if (document.activeElement.type !== 'checkbox') return;
			document.activeElement.blur();
		}

		let prevent_default = false;
		let wrappers = panel.querySelectorAll('.chord-button-wrapper,.playback-button-wrapper');
		wrappers.forEach(function(wrapper){
			let [code, ctrl, alt, shift] = wrapper.getAttribute('data-keysensor').split(',');
			if (e.code === 'ShiftLeft' || e.code === 'ShiftRight') shift = '1';
			if (e.code === code && e.ctrlKey === (ctrl === '1') && e.altKey === (alt === '1') && e.shiftKey === (shift === '1'))
			{
				if (e.repeat) return; // from this forEach function
				wrapper.querySelector('button').dispatchEvent(new PointerEvent('pointerdown', { bubbles: true }));
				wrapper.style.transform = 'translateY(0.5em)';
				prevent_default = true;
			}
		});
		if (prevent_default) { e.preventDefault(); return; }
		
		if (e.code === 'ShiftLeft' || e.code === 'ShiftRight')
		{
			for (let wrapper of wrappers)
			{
				let shift = wrapper.getAttribute('data-keysensor').split(',')[3];
				if (shift === '1') return;
				// We can't let the layout process Shift because it is used in a key binding
			}
		}
		
		let response = layout(e);
		
		if (response === null) return;
		
		if (response?.action === 'none') { e.preventDefault(); return false; }
		if (response?.action === 'record')
		{
			panel.querySelector('.recordbox').click();
			return false;
		}
		if (response?.action === 'sustain')
		{
			let susbox = panel.querySelector('#susbox');
			susbox.checked = !susbox.checked;
			return false;
		}
		if (response?.action === 'octave_down')
		{
			let octaves = panel.querySelector('#octaves_selector');
			const i = octaves.selectedIndex;
			if (i > 0) octaves.selectedIndex = i-1;
			return false;
		}
		if (response?.action === 'octave_up')
		{
			let octaves = panel.querySelector('#octaves_selector');
			const i = octaves.selectedIndex;
			if (i < 5) octaves.selectedIndex = i+1;
			return false;
		}
		if (response?.action === 'voice_down')
		{
			let voices = panel.querySelector('select#voiceselector');
			let i = voices.selectedIndex;
			let n = voices.options.length;
			voices.selectedIndex = (i + 1) % n;
			voices.dispatchEvent(new Event('change', { bubbles: true }));
			return false;
		}
		if (response?.action === 'voice_up')
		{
			let voices = panel.querySelector('select#voiceselector');
			let i = voices.selectedIndex;
			let n = voices.options.length;
			voices.selectedIndex = (n + i - 1) % n;
			voices.dispatchEvent(new Event('change', { bubbles: true }));
			return false;
		}
		if (response?.offsets)
		{
			if (e.repeat) return false;
			response.offsets.forEach(press);
			return false;
		}
		
		if (e.code === 'F5') return false;
		
		return;
	}
}
function keyup_function(unpress, panel, layout)
{
	return function keyup(e)
	{
		let prevent_default = false;
		panel.querySelectorAll('.chord-button-wrapper,.playback-button-wrapper').forEach(function(wrapper){
			let [code, ctrl, alt, shift] = wrapper.getAttribute('data-keysensor').split(',');
			if (e.code === 'ShiftLeft' || e.code === 'ShiftRight') shift = '0';
			if (e.code === code && e.ctrlKey === (ctrl === '1') && e.altKey === (alt === '1') && e.shiftKey === (shift === '1'))
			{
				wrapper.querySelector('button').dispatchEvent(new PointerEvent('pointerup', { bubbles: true }));
				wrapper.style.transform = '';
				prevent_default = true;
			}
		});
		if (prevent_default) { e.preventDefault(); return; }

		if (document.activeElement.nodeName === 'TEXTAREA') return;
		if (document.activeElement.nodeName === 'INPUT') return;
		//{
		//	if (document.activeElement.type !== 'checkbox') return;
		//	document.activeElement.blur();  
		//}
		let response = layout(e);
		if (response?.offsets)
		{
			response.offsets.forEach(unpress);
			return false;
		}
		
		if (e.code === 'F5') return false;
		
		return;
	}
}

function getLayoutById(id)
{
	for (let layout of g_layouts) if (layout.id === id) return layout;
	return g_layouts[0];
}
"use strict";

function openFullscreen(elem) {
	elem = elem || document.documentElement;
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
}
function closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.mozCancelFullScreen) { /* Firefox */
    document.mozCancelFullScreen();
  } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { /* IE/Edge */
    document.msExitFullscreen();
  }
}

function getNoteName(pc) {
	let noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
	if (g_lang === 'es') noteNames = ['Do', 'Do#', 'Re', 'Re#', 'Mi', 'Fa', 'Fa#', 'Sol', 'Sol#', 'La', 'La#', 'Si'];
	if (g_lang === 'ru') noteNames = ['До', 'До♯', 'Ре', 'Ре♯', 'Ми', 'Фа', 'Фа♯', 'Соль', 'Соль♯', 'Ля', 'Ля♯', 'Си'];
	function mod(a, b) { return ((a % b) + b) % b; }
	return noteNames[mod(pc,12)].replace('#', '♯');
}

function el(id) { return document.getElementById(id); }
function newel(tag) { return document.createElement(tag); }

let g_recorder = [];

function playaudio( noteObj )
{
	let n = noteObj.n;
	let delay = noteObj.delay;
	let voice, volume, sustain;
	
	if (noteObj.absolute)
	{
		voice = getVoiceById(noteObj.absolute.voice);
		volume = noteObj.absolute.volume / 2.5 * voice.volumeFactor; // from the 0 to 10 (in UI) converted to from 0 to 4 (in audioContext)
		sustain = noteObj.absolute.sustain;
	}
	else
	{
		let pianel = noteObj.pianel;
		voice = g_voices[ pianel.querySelector('#voiceselector').selectedIndex ];
		volume = pianel.querySelector('#volume_control').value / 2.5 * voice.volumeFactor;
		sustain = pianel.querySelector('#susbox').checked;
	}
	voice.play(n, delay/1000, volume, sustain);
	
	if (g_recordTracker.isAnyRecording() || delay > 0)
	{
		g_recorder.push({
			n: noteObj.n,
			delay: noteObj.delay + Date.now(),
			pianel: noteObj.pianel.id
		});
	}
	else
	{
		let now = Date.now();
		g_recorder = g_recorder.filter(x => x.delay > now);
	}
}

function svg_klawiatura(params)
{
	let firstoctave = params.firstoctave;
	let lastoctave = params.lastoctave;
	function svgtag(tag, attrs = {})
	{
		const element = document.createElementNS("http://www.w3.org/2000/svg", tag);
		for (const [key, value] of Object.entries(attrs)) element.setAttribute(key, value);
		return element;
	}
	function defs()
	{
		return ''+
		 '<defs>'
		+'	<linearGradient id="whitekey_gradient" x1="0%" y1="0%" x2="0%" y2="100%">'
		+'		<stop offset="0%" stop-color="#eee"/>'
		+'		<stop offset="100%" stop-color="white"/>'
		+'	</linearGradient>'
		+'	<linearGradient id="horizontalInset" x1="0%" y1="0%" x2="100%" y2="0%">'
		+'		<stop offset="0%" stop-color="rgba(0,0,0,0.1)" />  <stop offset="20%" stop-color="rgba(0,0,0,0)" />'
		+'		<stop offset="80%" stop-color="rgba(0,0,0,0)" />   <stop offset="100%" stop-color="rgba(0,0,0,0.1)" />'
		+'	</linearGradient>'
		+'	<linearGradient id="bottomInset" x1="0%" y1="0%" x2="0%" y2="100%">'
		+'  	<stop offset="96%" stop-color="rgba(0,0,0,0)" />'
		+'  	<stop offset="100%" stop-color="rgba(0,0,0,0.1)" />'
		+'	</linearGradient>'
		+'	<linearGradient id="topInset" x1="0%" y1="0%" x2="0%" y2="100%">'
		+'  	<stop offset="0%" stop-color="rgba(0,0,0,0.1)" />'
		+'  	<stop offset="4%" stop-color="rgba(0,0,0,0)" />'
		+'	</linearGradient>'
		+'	<linearGradient id="blackkey_gradient" x1="0%" y1="0%" x2="0%" y2="100%">'
		+'		<stop offset="0%" stop-color="#111"/>'
		+'		<stop offset="100%" stop-color="#444"/>'
		+'	</linearGradient>'
		+'</defs>';
	}
	function style()
	{
		return '<style type="text/css">\n'
		+'.whitekeybutton { cursor: pointer; }\n'
		+'.whitekeybutton text { font-family: Arial; fill: #777; }\n'
		+'svg.es_keywrite tspan:nth-child(1) { fill: #777; }\n'
		+'svg.es_keywrite tspan:nth-child(2) { fill: #aaa; }\n'

		+'svg.path_container path:nth-of-type(1) { stroke-width: 1; stroke: #aaa;        fill: url(#whitekey_gradient); }\n'
		+'svg.path_container path:nth-of-type(2) { stroke-width: 0; stroke: transparent; fill: url(#horizontalInset); }\n'
		+'svg.path_container path:nth-of-type(3) { stroke-width: 0; stroke: transparent; fill: url(#bottomInset); }\n'
		+'svg.path_container path:nth-of-type(4) { stroke-width: 0; stroke: transparent; fill: url(#topInset); }\n'

		+'.whitekeybutton:hover svg.path_container path:nth-of-type(1) { stroke-width: 1; stroke: #aaa;        fill: #ddd; }\n'
		+'.whitekeybutton:hover svg.path_container path:nth-of-type(2) { stroke-width: 0; stroke: transparent; fill: url(#horizontalInset); }\n'
		+'.whitekeybutton:hover svg.path_container path:nth-of-type(3) { stroke-width: 0; stroke: transparent; fill: url(#bottomInset); }\n'
		+'.whitekeybutton:hover svg.path_container path:nth-of-type(4) { stroke-width: 0; stroke: transparent; fill: url(#topInset); }\n'
		+'.whitekeybutton:hover text { font-family: Arial; fill: black; }\n'
		+'.whitekeybutton:hover svg.es_keywrite tspan:nth-child(1) { fill: black; }\n'
		+'.whitekeybutton:hover svg.es_keywrite tspan:nth-child(2) { fill: black; }\n'

		+'.blackkeybutton { cursor: pointer; }\n'
		+'.blackkeybutton path { fill: url(#blackkey_gradient); }\n'
		+'.blackkeybutton:hover path { fill: #555; }\n'

		+'.whitekeybutton.marked_key svg.path_container path:nth-of-type(1) { fill: rgb(255, 224, 166); }\n'
		+'.whitekeybutton.marked_key:hover svg.path_container path:nth-of-type(1) { fill: orange; }\n'

		+'.blackkeybutton.marked_key path { fill: orange; }\n'
		+'.blackkeybutton.marked_key:hover path { fill: #ffd17f; }\n'

		+'.whitekeybutton.chord_key svg.path_container path:nth-of-type(1) { fill: #DEC20B; }\n'
		+'.whitekeybutton:hover.chord_key svg.path_container path:nth-of-type(1) { fill: #DEC20B; }\n'
		+'.blackkeybutton.chord_key path { fill: #DEC20B; }\n'
		+'.blackkeybutton:hover.chord_key path { fill: #DEC20B; }\n'

		+'.whitekeybutton.offscale { opacity: 0.2; }\n'
		+'.whitekeybutton:hover.offscale { opacity: 1; }\n'
		+'.blackkeybutton.offscale { opacity: 0.2; }\n'
		+'.blackkeybutton:hover.offscale { opacity: 1; }\n'

		+'svg { overflow: visible; }\n'
		'</style>';
	}
	function isblackkey(n)
	{
		let i = n % 12;
		return (i==1 || i==3 || i==6 || i==8 || i==10);
	}

	function keywidth(n)
	{
	 switch (n%12)
	 {
	  case 0 : return 23/(23+24+23)*100;
	  case 2 : return 24/(23+24+23)*100;
	  case 4 : return 23/(23+24+23)*100 ;
	  
	  case 1 : return 14/(23+24+23)*100;
	  case 3 : return 14/(23+24+23)*100;
	  
	  case  5 : return 24/(24+23+23+24)*100;
	  case  7 : return 23/(24+23+23+24)*100;
	  case  9 : return 23/(24+23+23+24)*100;
	  case 11 : return 24/(24+23+23+24)*100;
	  
	  case  6 : return 14/(24+23+23+24)*100;
	  case  8 : return 14/(24+23+23+24)*100;
	  case 10 : return 14/(24+23+23+24)*100;
	 } 
	}

	function keyx(n)
	{
		switch (n%12)
		{
			// white keys
			case 0: return 0;
			case 2: return keywidth(0);
			case 4: return keywidth(0) + keywidth(2);
			
			case  5: return 0;
			case  7: return keywidth(5);
			case  9: return keywidth(5) + keywidth(7);
			case 11: return keywidth(5) + keywidth(7) + keywidth(9);
			
			// black keys
			case 1: return 14/(23+24+23)*100;
			case 3: return 100-14/(23+24+23)*100 - keywidth(3);
			
			case  6: return 13/(24+23+23+24)*100;
			case  8: return 40/(24+23+23+24)*100;
			case 10: return 100-13/(24+23+23+24)*100 - keywidth(10);
		}
	}

	function whitekeywrite_C(n)
	{
		let letter = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B','Cb'][n%12];
		return ("<svg x='37%' y='67' width='30%'>"
		+"<svg width='100%' height='100%' viewBox='0 0 13 20'>"
		+"<text x='0' y='13'>"+letter+"</text></svg></svg>");
	}
	function whitekeywrite_C_4(n)
	{
		let letter = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B','Cb'][n%12];
		let number = Math.floor(n/12)-1;
		return ("<svg x='25%' y='67' width='30%'>"
		+"<svg width='100%' height='100%' viewBox='0 0 13 20'>"
		+"<text x='0' y='13'>"+letter+"</text></svg></svg>"
		+"<svg x='55%' y='67' width='20%'>"
		+"<svg width='100%' height='100%' viewBox='0 0 11 30'>"
		+"<text x='0' y='25'>"+number+"</text></svg></svg>");
	}
	function whitekeywrite_C4(n)
	{
		let letter = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B','Cb'][n%12];
		let number = Math.floor(n/12)-1;
		return ("<svg x='25%' y='67' width='30%'>"
		+"<svg width='100%' height='100%' viewBox='0 0 13 20'>"
		+"<text x='0' y='13'>"+letter+number+"</text></svg></svg>");
	}
	function whitekeywrite_Do4(n)
	{
		let letters = ['Do', '', 'Re', '', 'Mi', 'Fa', '', 'Sol', '', 'La', '', 'Si'][n%12];
		let number = Math.floor(n/12)-1;
		let note = '<tspan>' + letters + '</tspan><tspan>' + number + '</tspan>';
		let x = '17%';
		if (letters === 'Mi') x = '22%';
		if (letters === 'Fa') x = '19%';
		if (letters === 'Sol') x = '15%';
		if (letters === 'La') x = '19%';
		if (letters === 'Si') x = '25%';
		return ("<svg class='es_keywrite' x='"+x+"' y='67' width='30%'>"
		+"<svg width='100%' height='100%' viewBox='0 0 13 20'>"
		+"<text x='0' y='13'>"+note+"</text></svg></svg>");
	}
	function keywrite(n)
	{
		let notes = params.notes;
		if (notes === 'Do4') return whitekeywrite_Do4(n);
		if (notes.startsWith('C_4')) return whitekeywrite_C_4(n);
		if (notes.startsWith('C4')) return whitekeywrite_C4(n);
		if (notes.startsWith('C')) return whitekeywrite_C(n);
		return '';
	}

	function dosolmi(n, ru)
	{
		function notenote(text, x)
		{
			let svg = svgtag('svg', {
				width: '100%', height: '100%',
				viewBox: '0 0 50 16',
				y: '35%'
			});
			let txt = svgtag('text', { x: x, y: 13, fill: 'black' });
			txt.innerHTML = text;
			svg.append(txt);
			return svg;
		}
		if (ru)	switch (n%12)
		{
			case 0 :  return notenote('до', 15);
			case 2 :  return notenote('ре', 15);
			case 4 :  return notenote('ми', 15);
			case 5 :  return notenote('фа', 13);
			case 7 :  return notenote('соль', 8);
			case 9 :  return notenote('ля', 16);
			case 11 : return notenote('си', 15);
		}
		switch (n%12)
		{
			case 0 :  return notenote('do', 16);
			case 2 :  return notenote('re', 17);
			case 4 :  return notenote('mi', 16);
			case 5 :  return notenote('fa', 17);
			case 7 :  return notenote('sol', 14);
			case 9 :  return notenote('la', 18);
			case 11 : return notenote('si', 17);
		}
	}

	function klawisz(n,extraC)
	{
		const button = svgtag('svg', {
			id: 'klawisz'+n,
			width: extraC ? '100%' : (keywidth(n)+'%'),
			height: isblackkey(n) ? '66.67%' : '100%',
			x: keyx(n)+'%',
			class: isblackkey(n) ? 'blackkeybutton' : 'whitekeybutton'
		});
		
		if (!isblackkey(n))
		{
			const container = svgtag('svg', {
				width: '100%', height: '100%',
				viewBox: "0 0 24 150", preserveAspectRatio: "none",
				class: 'path_container'
			});
			for (let i=1; i<=4; i++)
			{
				container.append(svgtag('path', {
					d: "M0,0 H24 V146 Q24,150 20,150 H4 Q0,150 0,146 Z"
				}));
			}
			button.append(container);
			button.innerHTML += keywrite(n);
			let notes = params.notes;
			if (notes.includes('_do')) button.append(dosolmi(n, null));
			if (notes.includes('_до')) button.append(dosolmi(n, 'ru'));
			return button;
		}
		
		const container = svgtag('svg', {
			width: '100%', height: '100%',
			viewBox: '0 0 14 100', preserveAspectRatio: 'none'
		});
		container.append(svgtag('path', {
			d: "M0,0 H14 V99 Q14,100 11,100 H3 Q0,100 0,99 Z"
		}));
		button.append(container);
		
		const rect = svgtag('rect', {
			x: '8%', y: '94%',
			width: '84%', height: '1',
			fill: '#777'
		});
		button.append(rect);
		return button;
	}
	function apply_marked_keys(kla)
	{
		let marked = params.marked;
		for (let n = 24; n <= 108; n++)
		{
			let key = kla.querySelector('#klawisz'+n);
			if (key && marked.includes(n)) key.classList.add('marked_key');
			
			let layout = getLayoutById(params.layout);
			if (key && layout?.offscale?.includes(n)) key.classList.add('offscale');
		}
	}

	const box = svgtag('svg', {
		//width: '', height: 300,
		preserveAspectRatio: "xMidYMid meet",
		viewBox: "0 0 " + ((lastoctave-firstoctave+1)*164+23) + ' 150'
	});
	box.innerHTML = defs() + style();
	box.append(svgtag('rect', { width: '100%', height: '100%', fill: 'transparent' }));
	
	const K = lastoctave-firstoctave+1;
	for (let i=firstoctave; i<=lastoctave; i++)
	{
		let CDE = svgtag('svg', {
			width: 3/(7*K+1)*100+'%',
			height: '100%',
			x: (i-firstoctave)*7/(7*K+1)*100+'%'
		});
		let c = 12*i+12;
		CDE.append(klawisz(c), klawisz(c+2), klawisz(c+4));
		CDE.append(klawisz(c+1), klawisz(c+3));
		let FGAB = svgtag('svg', {
			width: 4/(7*K+1)*100+'%',
			height: '100%',
			x: (3/(7*K+1)*100 + (i-firstoctave)*7/(7*K+1)*100) + '%'
		});
		let f = 12*i+12+5;
		FGAB.append(klawisz(f), klawisz(f+2), klawisz(f+4), klawisz(f+6));
		FGAB.append(klawisz(f+1), klawisz(f+3), klawisz(f+5))
		box.append(CDE, FGAB);
	}
	let extraC = svgtag('svg', {
		width: 1/(7*K+1)*100+'%',
		height: '100%',
		x: (lastoctave+1-firstoctave)*7/(7*K+1)*100+'%'
	});
	extraC.append(klawisz(12*(lastoctave+2),true));
	box.append(extraC);
	apply_marked_keys(box);
	return box;
}

function __pianopanel_presskey(key)
{
	key?.setAttribute('y', '4');
	key?.classList.remove('justpressed');
	key?.classList.add('justpressed');
}
function __pianopanel_unpresskey(key)
{
	key?.setAttribute('y', '0');
	setTimeout(function(){ key?.classList.remove('justpressed'); }, 500);
}

function touchActionNone_for_button_containers(el)
{
	el.style.userSelect = 'none';
	el.addEventListener('contextmenu', e => e.preventDefault(), { passive: false });
	
	el.style.touchAction = 'none'; // theoretically this should be sufficient
	// But we still need to prevent scrolling on iPad:
	el.addEventListener('touchmove', e => e.preventDefault(), { passive: false });
	// Let us prevent double-tap zoom for good measure:
	el.addEventListener('touchstart', e => e.preventDefault(), { passive: false });
	// the line above actually prevents blue selecting in Android Chrome
}

let g_unique_id_counter = 0;

function pianopanel(params)
{
	params.layout = params.layout ?? 'default_maximal';
	
	function uniqueID()
	{
		g_unique_id_counter++;
		return 'p' + g_unique_id_counter + Date.now().toString(36);
	}

	let chord_keys = [];
	
	function title_events(klapanel)
	{
		for (let n = 24; n <= 108; n++)
		{
			let letter = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B','B#'][n%12];
			let number = Math.floor(n/12)-1;

			let key = klapanel.querySelector('#klawisz'+n);
			if (key) key.addEventListener('pointerdown', function(e)
				{
					document.title = '(' + n + ') ' + letter + number;
				});
		}
	}

	function event_klawiatura()
	{
		let kla = svg_klawiatura(params);
		touchActionNone_for_button_containers(kla);
		for (let n = 24; n <= 108; n++)
		{
			let key = kla.querySelector('#klawisz' + n);
			function pointerdown(e)
			{
				let keys = [];
				for (let x of params.chord)
				{
					let chord_key = kla.querySelector('#klawisz' + (n+x));
					if (chord_key) keys.push(chord_key); else return; // chord does not fit
				}
				const key_delay = (params.delay > 0) ? 300 : 140;
				if (keys.length === 1) __pianopanel_presskey(key);
				else for (let i=0; i < keys.length; i++)
				{
					let chord_key = keys[i];
					setTimeout(function(){ __pianopanel_presskey(chord_key); chord_key.classList.add('marked_key'); }, i*params.delay);
					setTimeout(function(){ __pianopanel_unpresskey(chord_key); }, i*params.delay + key_delay);
				}
				for (let key of keys)
				{
					let marking_mode = panel.querySelector('.id-markbox')?.checked === true;
					if (keys.length === 1 && (e.ctrlKey || marking_mode)) key.classList.toggle('marked_key');
					if (keys.length > 1)
					{
						let allkeys = kla.querySelectorAll('.whitekeybutton, .blackkeybutton');
						allkeys.forEach((k) => k?.classList.remove('marked_key'));
					}
				}
			}
			function pointerup()
			{
				__pianopanel_unpresskey(key);
			}
			key?.addEventListener('pointerdown', pointerdown);
			key?.addEventListener('pointerup', pointerup);
			key?.addEventListener('pointerleave', pointerup);
		}
		return kla;
	}
	function chord_pointerdown(key)
	{
		let chord_label = panel.querySelector('#chord-label');
		if (panel.querySelector('#chordbox').checked === false)
		{
			chord_label.innerHTML = '';
			chord_label.style.display = 'none';
			return;
		}
		let n = parseInt(key.id.replace('klawisz',''));
		if (chord_keys.includes(n))
		{
			chord_keys = chord_keys.filter(x => x !== n);
			key.classList.remove('chord_key');
		}
		else
		{
			chord_keys.push(n);
			key.classList.add('chord_key');
		}
		playchord(chord_keys);
		let label = recognized_chord_name(chord_keys);
		chord_label.style.display = (label === '') ? 'none' : '';
		chord_label.innerHTML = label;
	}
	function apply_chord_pointerdown(kla)
	{
		for (let n = 24; n <= 108; n++)
		{
			let key = kla.querySelector('#klawisz' + n);
			key?.addEventListener('pointerdown', function(e){ chord_pointerdown(key); });
		}
	}

	function ostyluj(aa)
	{
		aa.classList.add('ostylowany');
	}
	function sustainbox()
	{
		let ss = newel('input');
		ss.id = 'susbox';
		ss.setAttribute("type", "checkbox");
		ss.checked = params.sustain;
		let aa = document.createElement('label');
		aa.append(words_Sustain(), ss);
		ostyluj(aa);
		return aa;
	}
	function octaves_selector()
	{
		let se = newel('select');
		se.id = 'octaves_selector';
		for (let i=1; i<=6; i++)
		{
			let opt = newel('option');
			let layout = getLayoutById(params.layout);
			let root = layout.tabPitchClass;
			let n = 12 + root + i * 12;
			let number = Math.floor(n/12)-1;
			opt.text = getNoteName(root) + number;
			se.add(opt);
		}
		se.selectedIndex = params.tab;
		
		let osel = newel('div');
		osel.classList.add('id-octaves-picker');
		osel.append(words_Tab_plays(), se);
		ostyluj(osel);
		osel.classList.add('niemagdyciasno');
		return osel;
	}
	function notation_selector()
	{
		let sel = newel('select');
		
		sel.id = 'notation_selector';
		sel.style.margin = '0.5em';
		function newopt(txt)
		{
			let opt = newel('option');
			opt.text = txt;
			opt.value = txt;
			return opt;
		}
		sel.add(newopt('none'));
		sel.add(newopt('C'));
		sel.add(newopt('C4'));
		sel.add(newopt('C_4'));
		
		sel.add(newopt('C_do'));
		sel.add(newopt('C4_do'));
		sel.add(newopt('C_4_do'));
		
		sel.add(newopt('C_до'));
		sel.add(newopt('C4_до'));
		sel.add(newopt('C_4_до'));
		
		sel.add(newopt('Do4'));
		for (let i = 0; i < sel.options.length; i++)
		{
			if (sel.options[i].value === params.notes)
				sel.selectedIndex = i;
		}
		sel.onchange = function(e) { panel.replaceWith(pianopanel(get_params())); }
		let osel = newel('div');
		osel.append(words_Note_names(), sel);
		ostyluj(osel);
		osel.classList.add('advanced_input');
		return osel;
	}
	function hookup_computer_keyboard()
	{
		let layout = g_layouts[panel.querySelector('.id-layout-picker').selectedIndex];
		document.body.onkeydown = keydown_function(press, panel, layout.function);
		document.body.onkeyup = keyup_function(unpress, panel, layout.function);
	}
	function LayoutPicker()
	{
		let se = newel('select');
		se.classList.add('id-layout-picker');
		for (let layout of g_layouts)
		{
			let opt = newel('option');
			opt.text = layout.name;
			se.add(opt);
		}
		se.selectedIndex = 0;
		for (let i = 0; i < g_layouts.length; i++)
			if (g_layouts[i].id === params.layout) se.selectedIndex = i;
		se.onchange = function(e) { panel.replaceWith(pianopanel(get_params())); }
		let osel = newel('div');
		osel.classList.add('niemagdyciasno');
		osel.append(words_Layout(), se);
		ostyluj(osel);
		return osel;
	}

	function voice_selector()
	{
		let se = newel('select');
		se.id = 'voiceselector';
		for (let voice of g_voices)
		{
			let opt = newel('option');
			opt.text = voice.name;
			se.add(opt);
		}
		se.selectedIndex = 0;
		for (let i = 0; i < g_voices.length; i++)
			if (g_voices[i].id === params.voice) se.selectedIndex = i;
		se.onchange = function(e) { voice().load(); };
		let osel = newel('div');
		osel.append(words_Voice(), se);
		ostyluj(osel);
		if (g_lang === 'ru') osel.style.fontSize = 'x-small';
		return osel;
	}
	function voice()
	{
		let i = panel.querySelector('#voiceselector').selectedIndex;
		return g_voices[i];
	}
	function volumeControlPanel()
	{
		const panel = newel('div');
		const val = newel('input');
		val.id = 'volume_control';
		val.disabled = true;
		val.type = 'text';
		val.style.margin = '0';
		val.value = params.volume;
		val.style.width = '2em';
		val.style.textAlign = 'center';
		val.style.background = 'white';
		val.style.color = 'black';
		if (g_isDark)
		{
			val.style.background = '#111';
			val.style.color = '#eee';			
		}
		val.style.fontWeight = 'bold';
		const lower = newel('button');
		lower.innerHTML = '&minus;';
		lower.title = words_volume_down();
		lower.addEventListener('pointerdown', function(e){
			val.value = Math.max(0, parseInt(val.value) - 1);
		});
		lower.style.background = 'white';
		lower.style.color = 'black';
		if (g_isDark)
		{
			lower.style.background = '#111';
			lower.style.color = '#eee';
			lower.style.fontWeight = 'bold';
		}
		const higher = newel('button');
		higher.innerHTML = '&plus;';
		higher.title = words_volume_up();
		higher.addEventListener('pointerdown', function(e){
			val.value = Math.min(10, parseInt(val.value) + 1);
		});
		higher.style.background = 'white';
		higher.style.color = 'black';
		if (g_isDark)
		{
			higher.style.background = '#111';
			higher.style.color = '#eee';
			higher.style.fontWeight = 'bold';
		}		
		panel.append(words_Volume(), lower, val, higher);
		ostyluj(panel);
		return panel;
	}
	function playchord(chord)
	{
		for (let i = 0; i < chord.length; i++)
		{
			let x = chord[i];
			let delay = i * params.delay;
			playaudio({ n: x, delay: delay, pianel: panel });
		}		
	}
	function hookup()
	{
		voice().load();
		for (let n = 24; n <= 108; n++)
		{
			let klawisz = kla.querySelector('#klawisz' + n);
			if (klawisz === null) continue;
			function fits()
			{
				for (let x of params.chord)
				{
					let chord_key = panel.querySelector('#klawisz'+(n+x));
					if (chord_key === null) return false;
				}
				return true;
			}
			if (fits())
			{
				klawisz.addEventListener('pointerdown', function(e){
					if (panel.querySelector('#chordbox').checked) return;
					playchord([...params.chord].map(x => n+x));
				});
			}
			else klawisz.style.cursor = 'not-allowed';
		}
	}
	function press(k)
	{
		let i = panel.querySelector('#octaves_selector').selectedIndex;
		let n = k+(i+2)*12;
		let key = kla.querySelector('#klawisz' + n);

		if (params.chord.length > 1)
		{
			if (key === null) return;
			let event_params = { bubbles: true, cancelable: true, pointerType: 'mouse', isPrimary: true };
			const pointerDownEvent = new PointerEvent('pointerdown', event_params );
			key.dispatchEvent(pointerDownEvent);
			return;
		}

		if (key) __pianopanel_presskey(key);
		if (panel.querySelector('#chordbox').checked === false)
		{
			playaudio({ n: n, delay: 0, pianel: panel });
		}
		let letter = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B','B#'][n%12];
		let number = Math.floor(n/12)-1;
		document.title = '(' + n + ') ' + letter + number;
		if (key) chord_pointerdown(key);
	}
	function unpress(k)
	{
		let i = panel.querySelector('#octaves_selector').selectedIndex;
		let n = k+(i+2)*12;
		let key = kla.querySelector('#klawisz' + n);

		if (params.chord.length > 1)
		{
			if (key === null) return;
			let event_params = { bubbles: true, cancelable: true, pointerType: 'mouse', isPrimary: true };
			const pointerUpEvent = new PointerEvent('pointerup', event_params );
			key.dispatchEvent(pointerUpEvent);
			return;
		}

		if (key) __pianopanel_unpresskey(key);
	}
	function get_params()
	{
		let pa = { ...params };
		get_querySelecor_params(pa, panel);
		return pa;
	}
	function remove_lowest_octave()
	{
		let a = params.firstoctave;
		let b = params.lastoctave;
		if (a === b) return;
		let newparams = get_params();
		newparams.firstoctave = a + 1;
		panel.replaceWith(pianopanel(newparams));
	}
	function remove_highest_octave()
	{
		let a = params.firstoctave;
		let b = params.lastoctave;
		if (a === b) return;
		let newparams = get_params();
		newparams.lastoctave = b - 1;
		panel.replaceWith(pianopanel(newparams));
	}
	function add_lower_octave()
	{
		let a = params.firstoctave;
		if (a === 1) return;
		let newparams = get_params();
		newparams.firstoctave = a - 1;
		panel.replaceWith(pianopanel(newparams));
	}
	function add_higher_octave()
	{
		let b = params.lastoctave;
		if (b === 7) return;
		let newparams = get_params();
		newparams.lastoctave = b + 1;
		panel.replaceWith(pianopanel(newparams));
	}
	function remove_lowest_octave_button(named)
	{
		let bu = newel('button');
		bu.addEventListener('click', remove_lowest_octave);
		bu.innerHTML = named ? words_removelowest() : '&minus;';
		bu.classList.add('range_change_button');
		return bu;
	}
	function remove_highest_octave_button(named)
	{
		let bu = newel('button');
		bu.addEventListener('click', remove_highest_octave);
		bu.innerHTML = named ? words_removehighest() : '&minus;';
		bu.classList.add('range_change_button');
		return bu;
	}
	function add_lower_octave_button(named)
	{
		let bu = newel('button');
		bu.addEventListener('click', add_lower_octave);
		bu.innerHTML = named ? words_addlower() : '&plus;';
		bu.classList.add('range_change_button');
		return bu;
	}
	function add_higher_octave_button(named)
	{
		let bu = newel('button');
		bu.addEventListener('click', add_higher_octave);
		bu.innerHTML = named ? words_addhigher() : '&plus;';
		bu.classList.add('range_change_button');
		return bu;
	}
	function clone_button()
	{
		let bu = newel('button');
		bu.innerHTML = '&#127929;';
		bu.style.padding = '0';
		bu.addEventListener('click', function(e){
			let newparams = get_params();
			let old_id = newparams.id, new_id = uniqueID();
			newparams = JSON.parse(JSON.stringify(newparams).replaceAll(`"${old_id}"`,`"${new_id}"` ));
			panel.parentElement.insertBefore(pianopanel(newparams), panel.nextSibling);
		});
		let obu = newel('label');
		obu.append(bu, ' ' + words_clone_this_piano());
		//obu.title = words_clone_this_piano();
		ostyluj(obu);
		obu.classList.add('advanced_input');
		return obu;
	}
	function delete_button()
	{
	    let bu = newel('button');
	    bu.innerHTML = 'X';
	    bu.addEventListener('click', function(e) {
			if (document.querySelectorAll('.piano-panel').length === 1) return;
	        if (confirm(words_Remove_this_piano())) panel.remove();
        });
		let obu = newel('label');
		obu.append(bu, ' ' + words_remove_this_piano());
		//obu.title = words_remove_this_piano();
		ostyluj(obu);
		obu.classList.add('advanced_input');
        return obu;
	}
	function ChordBox()
	{
		let box = newel('input'); box.type = 'checkbox';
		box.id = 'chordbox';
		box.onchange = function(){
			if (chord_keys.length > 0)
			{
				let chord_button = {
					pianel: panel,
					type: 'chord',
					notes: chord_keys,
					playback: 0,
					keysensor: ',0,0,0'
				};
				let memorybuttons = panel.querySelector('.memorybuttons');
				let bu = chord_button_wrapper(chord_button);
				memorybuttons.append(bu);
				memorybuttons.style.display = '';
				spinButton(bu);
			}
			chord_keys = [];
			panel.querySelectorAll('.whitekeybutton,.blackkeybutton').forEach(function(key){
				__pianopanel_unpresskey(key);
				key.classList.remove('chord_key');
			});
			panel.querySelector('#chord-label').style.display = 'none';
		};
		let labox = newel('label');
		labox.append(words_CHORD(), box);
		ostyluj(labox);
		if (params.chord.length > 1) labox.style.display = 'none';
		return labox;
	}
	function RecordBox()
	{
		let start = 0;
		let box = newel('input'); box.type = 'checkbox';
		box.classList.add('recordbox');
		box.onchange = function(){
			if (this.checked)
			{
				start = Date.now();
				this.parentElement.classList.add('redblink');
				params.recordOn = true;
				return;
			}
			params.recordOn = false;
			this.parentElement.classList.remove('redblink');
			let finish = Date.now();
			let notes = g_recorder.filter(x => x.delay >= start && x.delay <= finish);
			if (notes.length === 0) return;
			let bu = playback_button_wrapper({notes: notes, keysensor:',0,0,0', loop: ''});
			let memorybuttons = panel.querySelector('.memorybuttons');
			memorybuttons.append(bu);
			memorybuttons.style.display = '';
			spinButton(bu);
		};
		let labox = newel('label');
		labox.classList.add('recordbox-label'); // to make it disappear on chords pages
		labox.append(words_RECORD(), box);
		ostyluj(labox);
		//if (params.chord.length > 1) labox.style.display = 'none';
		return labox;
	}
	function SaveDownload()
	{
		let bu = newel('button');
		bu.append(words_SaveDownload()+' ', download_icon());
		bu.title = words_SaveDownloadTooltip();
		bu.onclick = download_save_all;
		ostyluj(bu);
		bu.style.fontSize = '100%';
		return bu;
	}
	function download_image_widget()
	{
		function downloadSVG(svgElement, width, height, filename)
		{
			const serializer = new XMLSerializer();
			let svgString = '<?xml version="1.0" encoding="UTF-8"?>\n';
			svgString += '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n';
			svgString += serializer.serializeToString(svgElement);
			//svgString = svgString.replace('<svg ', '<svg xmlns="http://www.w3.org/2000/svg" '); // idiotic to satisfy validator
			svgString = svgString.replace('preserveAspectRatio="xMidYMid meet" ', '');
			let svgprefix = '\n<svg xmlns="http://www.w3.org/2000/svg" ';
			svgString = svgString.replace(svgprefix, `${svgprefix} width='${width}' height='${height}' `);
			
			const blob = new Blob([svgString], {type: "image/svg+xml;charset=utf-8"});
			const url = URL.createObjectURL(blob);
			const downloadLink = document.createElement("a");
			downloadLink.href = url;
			downloadLink.download = filename + ".svg";
			downloadLink.click();
			URL.revokeObjectURL(url);
		}
		function convert_and_download(svgElement, width, height, type, filename)
		{
			const svgString = new XMLSerializer().serializeToString(svgElement);
			const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
			const url = URL.createObjectURL(svgBlob);

			const image = new Image();
			image.onload = function() {
				const canvas = document.createElement('canvas');
				canvas.width = width;
				canvas.height = height;
				const ctx = canvas.getContext('2d');
				ctx.drawImage(image, 0, 0);

				URL.revokeObjectURL(url);

				canvas.toBlob(function(blob) {
					const link = document.createElement('a');
					link.download = filename + '.' + type;
					link.href = URL.createObjectURL(blob);
					link.click();
				}, 'image/' + type);
			};
			image.src = url;
		}

		let formu = newel('form');
		formu.classList.add('advanced_input');
		let bu = newel('button');
		bu.type = 'submit';
		bu.innerHTML = words_Download_image();
		ostyluj(formu);
		function height()
		{
			let label = newel('label');
			let input = newel('input');
			input.style.width = '2em';
			input.name = 'height';
			input.value = '300';
			input.onchange = function(e) {
				let h = parseInt(this.value);
				if (isNaN(h) || h < 10) h = 300;
				this.value = h;
			};
			label.append(words_Pixel_height(), input);
			return label;
		}
		function type_select()
		{
			let label = newel('label');
			let sel = newel('select');
			function newopt(txt)
			{
				let opt = newel('option');
				opt.text = txt;
				opt.value = txt;
				return opt;
			}
			sel.append(newopt('svg'), newopt('webp'), newopt('png'));
			label.append(words_Image_type(), sel);
			return label;
		}
		formu.append(bu, height(), type_select());
		formu.target = '_blank';
		formu.onsubmit = function(e)
		{
			let viewBox_width = parseInt(kla.getAttribute('viewBox').split(' ')[2]);
			let viewBox_height = parseInt(kla.getAttribute('viewBox').split(' ')[3]);
			let width = Math.floor(viewBox_width * parseInt(formu.height.value) / viewBox_height);
			let height = formu.height.value;
			let type = formu.querySelector('select').value;
			function filename()
			{
				let a = params.firstoctave;
				let b = params.lastoctave;
				let marked = [];
				for (let n of get_params().marked)
				{
					let letter = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B','Cb'][n%12];
					let number = Math.floor(n/12)-1;
					marked.push( letter + number );
				}
				let marking = (marked.length === 0) ? '' : ('_marking_' + marked.join('_'));
				return 'piano_from_C' + a + '_to_C' + (b+1) + marking + '_' + width + 'x' + height;
			}
			if (type === 'svg')
			{
				downloadSVG(kla, width, height, filename());
				return false;
			}
			convert_and_download(kla, width, height, type, filename());
			return false;
		}
		return formu;
	}
	function chords_bookmark()
	{
		let div = newel('div');
		div.classList.add('id-chords-bookmark');
		div.style.display = 'none';
		let a = newel('a');
		a.target = '_blank';
		a.innerText = words_bookmark_yourchords();
		let p = newel('p');
		p.innerText = words_bookmark_explained();
		p.style.textAlign = 'left';
		div.append(a, p);
		div.style.maxWidth = '20em';
		return div;
	}
	function advanced_modal()
	{
		function octaves()
		{
			let div = newel('fieldset');
			let leg = newel('legend');
			leg.innerText = words_AdjustRange();
			div.append(leg);
			let buttons = [add_lower_octave_button('named'), add_higher_octave_button('named'), remove_lowest_octave_button('named'), remove_highest_octave_button('named')];
			for (let bu of buttons)
			{
				bu.style.display = 'inline-block';
				bu.style.margin = '0.5em';
				div.append(bu);
			}
			return div;
		}
		function multiple()
		{
			let div = newel('fieldset');
			let leg = newel('legend');
			leg.innerText = words_ManageMultiple();
			div.append(leg);
			let buttons = [clone_button(), delete_button()];
			for (let bu of buttons)
			{
				bu.style.display = 'inline-block';
				bu.style.margin = '0.5em';
				div.append(bu);
			}
			return div;		
		}
		function silence()
		{
			let bu = newel('button');
			bu.innerText = words_Silence();
			bu.style.padding = '0.5em';
			bu.style.margin = '0.5em';
			bu.onclick = enforceSilence;
			let div = newel('div');
			div.append(bu, words_StopsAll());
			return div;
		}
		function removeAds_div()
		{
			let div = newel('div');
			div.style.borderTop = '1px solid black';
			div.style.paddingTop = '0.5em';
			let bu = newel('button');
			bu.innerText = words_RemoveAds();
			bu.onclick = removeAds;
			let a = newel('a');
			a.href = '/contact#supportme';
			a.target = '_blank';
			a.innerText = words_PleaseDonate();
			div.append(words_YouCan(), bu, words_but(), a ,words_regularly());
			return div;
		}
		function exportimport()
		{
			let div = newel('div');
			div.append(SaveDownload(), importButton());
			return div;
		}
		function markbox()
		{
			let box = newel('input'); box.type = 'checkbox';
			box.classList.add('id-markbox');
			let labox = newel('label');
			labox.append(MarkKeys(), box, newel('br'), MarkKeysTip());
			return labox;
		}
		function shapeshift()
		{
			let bu = newel('button');
			bu.innerText = words_FlipLayout();
			bu.onclick = function()
			{
				let first = panel.childNodes[0];
				let second = panel.childNodes[1];
				panel.insertBefore(second, first);
				first.style.marginTop = '1em';
			};
			let labu = newel('label');
			labu.append(bu, ' ' + words_FlipLayout_Info());
			return labu;
		}
		function fullscreen()
		{
			let bu = newel('button');
			bu.innerText = words_EnterFullscreen();
			bu.onclick = function()
			{
				openFullscreen(panel);
				panel.style.backgroundColor = getComputedStyle(document.body).backgroundColor;
				close_advanced_modal();
			};
			let bu2 = newel('button');
			bu2.innerText = words_ExitFullscreen();
			bu2.onclick = function()
			{
				closeFullscreen();
				close_advanced_modal();
			};
			bu2.style.marginLeft = '1em';
			let div = newel('fieldset');
			let leg = newel('legend'); leg.innerText = words_Fullscreen();
			div.append(leg, bu, bu2);
			return div;
		}
		function close_advanced_modal()
		{
			panel.querySelector('.advanced_modal').style.display = 'none';
		}
		
		let div = newel('div');
		div.style.display = 'none';
		div.classList.add('advanced_modal');
		div.classList.add('modal-background');
		let modal = newel('div');
		modal.classList.add('modal-window');
		div.append(modal);
		modal.onclick = function(e) { e.stopPropagation(); }
		div.onclick = function(){ div.style.display = 'none'; };
		let bu = newel('button');
		bu.innerHTML = words_Close();
		bu.style.padding = '0.5em';
		bu.style.fontSize = '100%';
		bu.onclick = function(e) { div.style.display = 'none'; }
		
		let elems = [bu, silence(), octaves(), multiple(), markbox(), notation_selector(),
					download_image_widget(), exportimport(), shapeshift(), fullscreen(), chords_bookmark(), removeAds_div()];
		for (let elem of elems)
		{
			let odiv = newel('div');
			odiv.append(elem);
			odiv.style.margin = '2em auto 2em auto';
			modal.append(odiv);
		}

		return div;
	}
	function advanced_button(mini)
	{
		let bu = newel('button');
		let span = newel('span'); span.classList.add('niemagdyciasno');
		span.innerText = words_MoreFeatures();
		//bu.innerHTML = '&#128295; ' + (mini ? '' : words_MoreFeatures()) + ' &#128296;';
		bu.innerHTML = '&#128295; ' + span.outerHTML + ' &#128296;';
		ostyluj(bu);
		bu.onclick = function(e) {
			let chords = bookmark_chords_query(panel);
			let bookmark = panel.querySelector('.id-chords-bookmark');
			bookmark.style.display = 'none';
			if (chords)
			{
				let prefix = "https://virtualpiano.online/";
				if (g_lang === 'es') prefix += 'teclado/';
				if (g_lang === 'ru') prefix += 'pianino/';
				bookmark.querySelector('a').href = prefix + chords;
				bookmark.style.display = 'block';
			}
			panel.querySelector('.advanced_modal').style.display = 'block';
		};
		return bu;
	}

	let panel = newel('div');
	panel.id = params.id || uniqueID();
	panel.className = 'piano-panel';
	panel.style.userSelect = 'none';
	panel.style.marginBottom = '2em';
	let kla = event_klawiatura();
	title_events(kla);
	apply_chord_pointerdown(kla);

	function inputs_css()
	{
		let basic = newel('div');
		basic.classList.add('id-basic_inputs_div');
		basic.append(octaves_selector(), volumeControlPanel(), sustainbox(), voice_selector());
		let advanced = newel('div');
		advanced.id = 'advanced_inputs_div';
		let Export = SaveDownload(); Export.classList.add('niemagdyciasno');
		advanced.append(LayoutPicker(), advanced_modal(), advanced_button(), ChordBox(), RecordBox(), Export);
		let inputs = newel('div');
		inputs.classList.add('inputs');
		inputs.style.textAlign = 'center';
		inputs.append(advanced, basic);
		advanced.style.marginBottom = '0.5em';
		advanced.style.lineHeight = '2';
		basic.style.marginBottom = '0.5em';
		basic.style.lineHeight = '2';
		return inputs;
	}
	function klawa_css()
	{
		let klawa = newel('div'); klawa.classList.add('klawa');
		let td1 = newel('div'); td1.classList.add('pianoside');
		let td3 = newel('div'); td3.classList.add('pianoside');
		klawa.append(td1, kla, td3);
		td1.append(add_lower_octave_button(), remove_lowest_octave_button());
		td3.append(add_higher_octave_button(), remove_highest_octave_button());
		klawa.style.position = 'relative'; klawa.append(chord_label());
		return klawa;
	}
	
	panel.append(inputs_css(), klawa_css());
	
	hookup();

	function apply_shape()
	{
		function hide_sides() { panel.querySelector('.klawa').classList.add('nosides'); }
		function show_sides() { panel.querySelector('.klawa').classList.remove('nosides'); }
		let isMobile = (window.innerWidth <= 767);
		if (isMobile)
		{
			params.shape = params.shape % 2;
			if (params.shape === 1) params.shape = 3;
		}
		if (params.shape === 0)
		{
			hide_sides();
			panel.querySelectorAll('.id-basic_inputs_div')?.forEach(x => x.style.display = 'none');
			panel.querySelectorAll('#advanced_inputs_div')?.forEach(x => x.style.display = 'none');
			return;
		}
		if (params.shape === 1)
		{
			show_sides();
			panel.querySelectorAll('.id-basic_inputs_div')?.forEach(x => x.style.display = 'none');
			panel.querySelectorAll('#advanced_inputs_div')?.forEach(x => x.style.display = 'none');
			return;
		}
		if (params.shape === 2)
		{
			show_sides();
			panel.querySelectorAll('.id-basic_inputs_div')?.forEach(x => x.style.display = '');
			panel.querySelectorAll('#advanced_inputs_div')?.forEach(x => x.style.display = 'none');
			return;
		}
		if (params.shape === 3)
		{
			show_sides();
			panel.querySelectorAll('.id-basic_inputs_div')?.forEach(x => x.style.display = '');
			panel.querySelectorAll('#advanced_inputs_div')?.forEach(x => x.style.display = '');
			return;
		}
	}
	function shape_toggle()
	{
		let bu = newel('button');
		bu.id = 'shape_toggle';
		bu.style.display = 'none';
		bu.onclick = function() {
			params.shape = (params.shape + 1) % 4;
			apply_shape();
		};
		return bu;
	}
	function chord_setter()
	{
		let bu = newel('button');
		bu.id = 'chord_setter';
		bu.style.display = 'none';
		bu.onclick = function() {
			let params = get_params();
			let [chord_data, delay_data] = bu.innerText.split(' ');
			params.chord = chord_data.split(',').map(x => parseInt(x));
			params.delay = parseInt(delay_data);
			panel.replaceWith(pianopanel(params));
		};
		return bu;
	}
	function memorybuttons_div()
	{
		let div = newel('div');
		div.classList.add('memorybuttons');
		div.style.marginTop = '0.5em';
		div.style.position = 'relative';
		div.style.resize = 'vertical';
		div.style.overflow = 'auto';
		div.style.display = 'none';
		div.style.border = '1px dotted black';
		div.style.textAlign = 'left';
		let max = 0;
		params.memorybuttons.forEach( function(membu){
			if (membu.type === 'chord') div.append(chord_button_wrapper(membu));
			if (membu.type === 'playback') div.append(playback_button_wrapper(membu));
			div.style.display = '';
			if (membu.position) max = Math.max(max, parseInt(membu.position.y));
		});
		div.style.minHeight = max + 50 + 'px';
		return div;
	}
	function chord_label()
	{
		let div = newel('div');
		div.id = 'chord-label';
		div.style.position = 'absolute';
		div.style.top = '-0.5em';
		div.style.left = '50%';
		div.style.transform = 'translateX(-50%)';
		div.style.background = "gray";
		div.style.color = 'white';
		div.style.fontWeight = 'bold';
		div.style.padding = '0.5em';
		div.style.borderRadius = '0.5em';
		div.style.fontFamily = 'Verdana';
		div.style.userSelect = 'text';
		div.style.display = 'none';
		return div;
	}
	panel.append(memorybuttons_div());
	panel.append(shape_toggle(), chord_setter()); // invisible
	panel.querySelector('#chord_setter').innerText = params.chord.join(',') + ' ' + params.delay;
	apply_shape();
	hookup_computer_keyboard();
	kla.addEventListener('click', hookup_computer_keyboard);
	if (params?.recordOn)
	{
		panel.querySelector('.recordbox').checked = true;
		panel.querySelector('.recordbox').parentElement.classList.add('redblink');
	}
	return panel;
}

function default_note_names()
{
	if (g_lang === 'es') return 'Do4';
	if (g_lang === 'ru') return 'C4_до';
	return 'C4';
}
function default_pianopanel_params()
{
	let isMobile = (window.innerWidth <= 767);
	
	function chordbuttons_from_query()
	{
		let c = (new URLSearchParams(location.search)).get('c');
		if (c === null) return [];
		let chords = c.split('_');
		if (chords.length === 0) return [];
		let arr = [];
		for (let chord of chords)
		{
			let parsed_chord = [];
			for (let key of chord.split('-'))
			{
				key = parseInt(key);
				if (isNaN(key)) return [];
				if (key < 24 || key > 108) return [];
				parsed_chord.push(key);
			}
			arr.push({
				type: 'chord',
				notes: parsed_chord,
				keysensor: ',0,0,0',
				playback: 0
			});
		}
		return arr;
	}
	let ret = {
		layout: 'default_maximal',
		firstoctave: (isMobile) ? 3 : 2,
		lastoctave: (isMobile) ? 4 : 5,
		voice: 'piano7',
		volume: 5,
		sustain: 1,
		tab: 2,
		marked: [],
		notes: default_note_names(),
		chord: [0],
		delay: 0,
		shape: 3,
		memorybuttons : chordbuttons_from_query(),
		recordOn: false
	};
	if (ret.memorybuttons.length > 0) ret.marked = ret.memorybuttons[0].notes;
	return ret;
}

function open_chord_button_editor(wrapper)
{
	let membu = wrapper.querySelector('button');
	let modal = newel('div');
	modal.classList.add('modal-background');
	modal.onclick = function() { modal.remove(); };
	let editor = newel('div');
	editor.classList.add('modal-window');
	editor.onclick = function(e) { e.stopPropagation(); }
	let cancel = newel('button');
	cancel.innerHTML = '&#10060;';
	cancel.append(words_Cancel());
	cancel.onclick = function(){ modal.remove(); };
	function save()
	{
		function sanitize(x)
		{
			let sup1 = 'hdgftrtfg64fd67t6tt56fhgfvdftyv';
			let sup2 = 'gdfd53fd88yf54fgtdtfrte66rt33r2';
			let sub1 = 'hdhdhgttfdfdrt55e6we6e6e66e6e6x';
			let sub2 = 'udududududyeyeydggd7673636rtdtd';
			let span = newel('span');
			span.innerText = x.replaceAll('<sup>', sup1).replaceAll('</sup>', sup2).
								replaceAll('<sub>', sub1).replaceAll('</sub>', sub2);
			return span.innerHTML.replaceAll(sup1, '<sup>').replaceAll(sup2, '</sup>').
								replaceAll(sub1, '<sub>').replaceAll(sub2, '</sub>');
		}
		let bu = newel('button');
		bu.innerHTML = '&#9989; ';
		bu.append(words_Save());
		bu.onclick = function(){
			membu.innerHTML = sanitize(modal.querySelector('#label_input').value || '___');
			wrapper.setAttribute('data-keysensor',
				modal.querySelector('#key-code').value +
				(modal.querySelector('#isCtrl').checked ? ',1' : ',0') +
				(modal.querySelector('#isAlt').checked ? ',1' : ',0') +
				(modal.querySelector('#isShift').checked ? ',1' : ',0')
			);
			wrapper.setAttribute('data-playback', get_playback());
			modal.remove();
		};
		return bu;
	}
	function label(txt)
	{
		let la = newel('label');
		la.style.display = 'block';
		let input = newel('input');
		input.id = 'label_input';
		input.type = 'text';
		input.style.width = '24em';
		input.style.margin = '1em';
		input.value = txt;
		la.append(words_Label() + ':', input);
		return la;
	}
	function Delete()
	{
		let bu = newel('button');
		bu.innerHTML = '&#9888;️';
		bu.append(words_Delete());
		bu.onclick = function() { wrapper.remove(); modal.remove(); };
		return bu;
	}
	function CloneButton()
	{
		let bu = newel('button');
		bu.innerText = words_Duplicate();
		bu.onclick = function() {
			let obj = chord_button_wrapper_to_object(wrapper);
			delete obj.position;
			let bu = chord_button_wrapper(obj);
			wrapper.parentElement.append(bu);
			spinButton(bu);
			modal.remove();
		};
		return bu;
	}
	function KeySensor()
	{
		let keysensor = wrapper.getAttribute('data-keysensor').split(',');
		
		let isCtrl = newel('input'); isCtrl.type = 'checkbox'; isCtrl.id = 'isCtrl';
		isCtrl.checked = (keysensor[1] === '1');
		let lCtrl = newel('label'); lCtrl.append('Ctrl', isCtrl);
		lCtrl.style.marginLeft = '1em';
		
		let isAlt = newel('input'); isAlt.type = 'checkbox'; isAlt.id = 'isAlt';
		isAlt.checked = (keysensor[2] === '1');
		let lAlt = newel('label'); lAlt.append('Alt', isAlt);
		lAlt.style.marginLeft = '1em';
		
		let isShift = newel('input'); isShift.type = 'checkbox'; isShift.id = 'isShift';
		isShift.checked = (keysensor[3] === '1');
		let lShift = newel('label'); lShift.append('Shift', isShift);
		lShift.style.marginLeft = '1em';
		
		let input = newel('input');
		input.type = 'text';
		input.id = 'key-code';
		input.value = keysensor[0];
		input.style.width = '7em';
		input.onkeydown = function(e){
			input.value = e.code;
			isAlt.checked = e.altKey;
			isCtrl.checked = e.ctrlKey;
			isShift.checked = e.shiftKey;
			e.preventDefault();
			if (e.code === 'Escape') input.value = '';
		};
		let div = newel('fieldset');
		div.style.display = 'table';
		div.style.marginTop = '1em';
		let legend = newel('legend');
		legend.innerText = words_AssignedKey();'Assigned computer keyboard key';
		div.append(legend, input, lCtrl, lAlt, lShift);
		return div;
	}
	function playback()
	{
		let div = newel('fieldset');
		div.style.display = 'table';
		div.style.marginTop = '2em';
		let legend = newel('legend'); legend.innerText = words_ChordPlaybackSettings();
		
		let playmode = newel('fieldset');
		playmode.style.margin = '1em';
		let playmodelegend = newel('legend'); playmodelegend.innerText = words_Playnotes();
		let input1 = newel('input'); input1.type = 'radio'; input1.name = 'playmode'; input1.value = 'together';
		let input2 = newel('input'); input2.type = 'radio'; input2.name = 'playmode'; input2.value = 'ascending';
		let input3 = newel('input'); input3.type = 'radio'; input3.name = 'playmode'; input3.value = 'descending';
		let label1 = newel('label'); label1.append(input1, words_allatonce());
		let label2 = newel('label'); label2.append(input2, words_inAscendingOrder());
		let label3 = newel('label'); label3.append(input3, words_inDescendingOrder());
		[label2, label3].forEach(x => x.style.marginLeft = '1em');
		playmode.append(playmodelegend, label1, label2, label3);

		let duration = newel('label');
		duration.style.display = 'table';
		duration.style.margin = '1em auto';
		let input4 = newel('input'); input4.type = 'number'; input4.min = '0'; input4.step = '100'; input4.id = 'duration';
		duration.append(words_DurationFromFirst2LastNote(), input4, words_ms());
		input4.style.width = '4em'; input4.style.marginLeft = '0.5em';
		
		let data = parseInt(wrapper.getAttribute('data-playback'));
		input1.checked = (data === 0);
		input2.checked = (data > 0);
		input3.checked = (data < 0);
		input4.value = Math.abs(data);
		input4.disabled = (data === 0);
		
		input1.onclick = function(){ input4.value = 0; input4.disabled = true; };
		[input2, input3].forEach( x => x.onclick = function(){ input4.disabled = false; input4.focus();  } );
		input4.onchange = function(){ if (this.value === '') { this.value = 0; input1.checked = true; } };

		div.append(legend, duration, playmode);
		return div;
	}
	function get_playback()
	{
		let minus = ('descending' === modal.querySelector('input[name="playmode"]:checked').value) ? '-' : '';
		return minus + modal.querySelector('#duration').value;
	}

	let top_buttons = newel('div');
	top_buttons.style = 'display: flex; justify-content: space-between; width: 100%; margin-bottom: 1em;';
	top_buttons.append(Delete(), cancel);
	let bottom_buttons = newel('div');
	bottom_buttons.style = 'display: flex; justify-content: space-between; width: 100%; margin-top: 2em;';
	bottom_buttons.append(CloneButton(), save());
	let header = newel('div');
	header.innerText = words_ChordButtonEditor();
	header.style = "font-weight: bold; font-size: larger; text-align: center; margin-bottom: 1em;";
	
	editor.append(header, top_buttons, label(membu.innerHTML), KeySensor(), playback(), bottom_buttons);
	modal.append(editor);
	document.body.append(modal);
}
function chord_button_wrapper_to_object(div)
{
	let obj = { type: 'chord' };
	obj.label = div.querySelector('button').innerHTML;
	obj.notes = JSON.parse(div.getAttribute('data-notes'));
	obj.keysensor = div.getAttribute('data-keysensor');
	obj.playback = parseInt(div.getAttribute('data-playback'));
	if (div.style.position === 'absolute')
		obj.position = { x: div.style.left , y: div.style.top };
	return obj;
}
function chord_button_wrapper(x)
{
	function pointerdown()
	{
		let pianel = this.closest('.piano-panel');
		let obj = chord_button_wrapper_to_object(this.parentElement);
		let delay = 0;
		if (obj.notes.length > 1) delay = obj.playback / (obj.notes.length - 1);
		if (delay < 0) { delay = -delay; obj.notes.reverse(); }
		for (let i=0; i < obj.notes.length; i++)
		{
			let n = obj.notes[i];
			let key = pianel.querySelector('#klawisz' + n);
			setTimeout(function(){
				__pianopanel_presskey(key);
				playaudio( { n: n, pianel: pianel, delay: 0 } );
			}, i*delay);
		}
	}
	function pointerup()
	{
		let pianel = this.closest('.piano-panel');
		let obj = chord_button_wrapper_to_object(this.parentElement);
		let delay = 0;
		if (obj.notes.length > 1) delay = obj.playback / (obj.notes.length - 1);
		if (delay < 0) { delay = -delay; obj.notes.reverse(); }
		for (let i=0; i < obj.notes.length; i++)
		{
			let n = obj.notes[i];
			let key = pianel.querySelector('#klawisz' + n);
			setTimeout(function(){
				__pianopanel_unpresskey(key);
			}, i*delay);
		}
	}
	function chord_button(x)
	{
		if (!x.label) x.label = chord_autonick(x.notes);
		
		let bu = newel('button');
		bu.style.height = '2em';
		bu.innerHTML = x.label;
		bu.addEventListener('pointerdown', pointerdown);
		bu.addEventListener('pointerup', pointerup);
		bu.addEventListener('pointerleave', pointerup);
		return bu;
	}

	let div = newel('div');
	div.classList.add('chord-button-wrapper');
	div.style.display = 'inline-block';
	div.style.verticalAlign = 'bottom';
	div.style.margin = '0.5em';
	div.setAttribute('data-notes', JSON.stringify(x.notes.sort()));
	div.setAttribute('data-keysensor', x.keysensor);
	div.setAttribute('data-playback', x.playback);
	let icon = settings_icon();
	icon.classList.add('settings-icon');
	icon.title = words_ButtonSettings();
	icon.style.width = '1em';
	icon.onclick = function() { open_chord_button_editor(div); };
	div.append(chord_button(x), icon);
	makeDraggable(div); // from /scripts/drag-and-drop.js
	if (x.position)
	{
		div.style.position = 'absolute';
		div.style.left = x.position.x;
		div.style.top = x.position.y;
	}
	return div;
}

function playback_button_wrapper(x)
{
	function metabolized_playback_object(obj)
	{
		let start = obj[0].delay;
		let arr = [];
		for (let noteObj of obj)
		{
			let newNote = { };
			newNote.n = noteObj.n;
			newNote.delay = noteObj.delay - start;
			newNote.pianel = noteObj.pianel; // it should be an ID as recorded by playaudio
			arr.push(newNote);
		}
		return arr;
	}
	const notes = metabolized_playback_object(x.notes);
		
	function playback_button()
	{
		const duration = notes[notes.length-1].delay;
		const button_color = g_isDark ? '#111' : '#eee';
		const playback_color = g_isDark ? '#f00' : '#faa';
		function pointerdown(e)
		{
			const bu = e.target;
			for (let note of notes)
			{
				let pianel = el(note.pianel) || document.querySelector('.piano-panel');
				let key = pianel.querySelector('#klawisz' + note.n);
				setTimeout(function(){ __pianopanel_presskey(key); }, note.delay);
				setTimeout(function(){ __pianopanel_unpresskey(key); }, note.delay + 100 );
				
				let percent = note.delay/duration*100+'%';
				let background = 'linear-gradient(to right, ' + button_color + ' ' + percent+", " + playback_color + " 0%)";
				setTimeout(function(){ bu.style.background = background; }, note.delay);
				
				playaudio({
					n: note.n,
					delay: note.delay,
					pianel: pianel
				});
			}
			function loop()
			{
				if (!bu.isConnected) return;
				if (bu?.closest('.playback-button-wrapper').getAttribute('data-loop') === 'endlessly')
				{
					let event_params = { bubbles: true, cancelable: true, pointerType: 'mouse', isPrimary: true };
					let pointerDownEvent = new PointerEvent('pointerdown', event_params );
					bu.dispatchEvent(pointerDownEvent);
				}
			}
			let delays = [];
			for (let note of notes) delays.push(Math.floor(note.delay / 500));
			delays = [...new Set(delays)];
			let pause = (delays.length === 1) ? 500 : Math.floor(duration / (delays.length - 1));
			setTimeout(loop, notes[notes.length-1].delay + pause);
		}
		function default_label()
		{
			if (notes.length <= 8)
			{
				let label = '';
				let pitches = [];
				notes.forEach(note => pitches.push(note.n));
				return '&#9654; ' + pitches.map(sound_name).join(' ');
			}
			let seconds = (duration/1000).toFixed(1);
			return seconds + 's &#9654;';
		}
		let bu = newel('button');
		bu.innerHTML = x.label || default_label();
		bu.style.background = button_color;
		bu.onpointerdown = pointerdown;
		return bu;
	}
		
	let div = newel('div');
	div.classList.add('playback-button-wrapper');
	div.style.display = 'inline-block';
	div.style.verticalAlign = 'bottom';
	div.style.margin = '0.5em';
	div.setAttribute('data-keysensor', x.keysensor);
	div.setAttribute('data-notes', JSON.stringify(notes));
	div.setAttribute('data-loop', x.loop || '');
	
	let icon = settings_icon();
	icon.classList.add('settings-icon');
	icon.title = words_ButtonSettings();
	icon.style.width = '1em';
	icon.onclick = function() { open_playback_button_editor(div); };
	div.append(playback_button(), icon);
	makeDraggable(div); // from /scripts/drag-and-drop.js
	if (x.position)
	{
		div.style.position = 'absolute';
		div.style.left = x.position.x;
		div.style.top = x.position.y;
	}
	return div;
}
function playback_wrapper_to_object(wrapper)
{
	let obj = { type: 'playback' };
	obj.keysensor = wrapper.getAttribute('data-keysensor');
	obj.notes = JSON.parse(wrapper.getAttribute('data-notes'));
	obj.label = wrapper.querySelector('button').innerHTML;
	obj.loop = wrapper.getAttribute('data-loop');
	if (wrapper.style.position === 'absolute')
		obj.position = { x: wrapper.style.left, y: wrapper.style.top };
	return obj;
}
function open_playback_button_editor(wrapper)
{
	let membu = wrapper.querySelector('button');
	let modal = newel('div');
	modal.classList.add('modal-background');
	modal.onclick = function() { modal.remove(); };
	let editor = newel('div');
	editor.classList.add('modal-window');
	editor.onclick = function(e) { e.stopPropagation(); }
	let cancel = newel('button');
	cancel.innerHTML = '&#10060;';
	cancel.append(words_Cancel());
	cancel.onclick = function(){ modal.remove(); };
	function save()
	{
		function sanitize(x)
		{
			let sup1 = 'hdgftrtfg64fd67t6tt56fhgfvdftyv';
			let sup2 = 'gdfd53fd88yf54fgtdtfrte66rt33r2';
			let sub1 = 'hdhdhgttfdfdrt55e6we6e6e66e6e6x';
			let sub2 = 'udududududyeyeydggd7673636rtdtd';
			let span = newel('span');
			span.innerText = x.replaceAll('<sup>', sup1).replaceAll('</sup>', sup2).
								replaceAll('<sub>', sub1).replaceAll('</sub>', sub2);
			return span.innerHTML.replaceAll(sup1, '<sup>').replaceAll(sup2, '</sup>').
								replaceAll(sub1, '<sub>').replaceAll(sub2, '</sub>');
		}
		let bu = newel('button');
		bu.innerHTML = '&#9989; ';
		bu.append(words_Save());
		bu.onclick = function(){
			membu.innerHTML = sanitize(modal.querySelector('#label_input').value || '___');
			wrapper.setAttribute('data-keysensor',
				modal.querySelector('#key-code').value +
				(modal.querySelector('#isCtrl').checked ? ',1' : ',0') +
				(modal.querySelector('#isAlt').checked ? ',1' : ',0') +
				(modal.querySelector('#isShift').checked ? ',1' : ',0')
			);
			wrapper.setAttribute('data-loop', modal.querySelector('.id-loopbox').checked  ? 'endlessly' : '');
			modal.remove();
		};
		return bu;
	}
	function label(txt)
	{
		let la = newel('label');
		la.style.display = 'block';
		let input = newel('input');
		input.id = 'label_input';
		input.type = 'text';
		input.style.width = '24em';
		input.style.margin = '1em';
		input.value = txt;
		la.append(words_Label() + ':', input);
		return la;
	}
	function Delete()
	{
		let bu = newel('button');
		bu.innerHTML = '&#9888;️';
		bu.append(words_Delete());
		bu.onclick = function() { wrapper.remove(); modal.remove(); };
		return bu;
	}
	function CloneButton()
	{
		let bu = newel('button');
		bu.innerText = words_Duplicate();
		bu.onclick = function() {
			let obj = playback_wrapper_to_object(wrapper);
			delete obj.position;
			let bu = playback_button_wrapper(obj);
			wrapper.parentElement.append(bu);
			spinButton(bu);
			modal.remove();
		};
		return bu;
	}
	function KeySensor()
	{
		let keysensor = wrapper.getAttribute('data-keysensor').split(',');
		
		let isCtrl = newel('input'); isCtrl.type = 'checkbox'; isCtrl.id = 'isCtrl';
		isCtrl.checked = (keysensor[1] === '1');
		let lCtrl = newel('label'); lCtrl.append('Ctrl', isCtrl);
		lCtrl.style.marginLeft = '1em';
		
		let isAlt = newel('input'); isAlt.type = 'checkbox'; isAlt.id = 'isAlt';
		isAlt.checked = (keysensor[2] === '1');
		let lAlt = newel('label'); lAlt.append('Alt', isAlt);
		lAlt.style.marginLeft = '1em';
		
		let isShift = newel('input'); isShift.type = 'checkbox'; isShift.id = 'isShift';
		isShift.checked = (keysensor[3] === '1');
		let lShift = newel('label'); lShift.append('Shift', isShift);
		lShift.style.marginLeft = '1em';
		
		let input = newel('input');
		input.type = 'text';
		input.id = 'key-code';
		input.value = keysensor[0];
		input.style.width = '7em';
		input.onkeydown = function(e){
			input.value = e.code;
			isAlt.checked = e.altKey;
			isCtrl.checked = e.ctrlKey;
			isShift.checked = e.shiftKey;
			e.preventDefault();
			if (e.code === 'Escape') input.value = '';
		};
		let div = newel('fieldset');
		div.style.display = 'table';
		div.style.marginTop = '1em';
		let legend = newel('legend');
		legend.innerText = words_AssignedKey();'Assigned computer keyboard key';
		div.append(legend, input, lCtrl, lAlt, lShift);
		return div;
	}
	function Loop()
	{
		let div = newel('div');
		div.style.margin = '1.5em 0';
		let labox = newel('label');
		let box = newel('input'); box.type = 'checkbox';
		box.classList.add('id-loopbox');
		box.checked = wrapper.getAttribute('data-loop') === 'endlessly';
		labox.append(words_Loop_endlessly(), box);
		div.append(labox);
		return div;
	}
	function DownloadAudio()
	{
		function download_python_midi()
		{
			function mididata()
			{
				let notes = JSON.parse(wrapper.getAttribute('data-notes'));
				for (let i = 0; i < notes.length; i++)
				{
					let note = notes[i];
					notes[i] = '' + note.n + ',' + (note.delay / 1000);
				}
				return notes.join(';');
			}
			function midiform()
			{
				const form = newel('form');
				form.method = 'POST'; form.action = '/scripts/midi/';
				const input = newel('input'); input.type = 'hidden';
				input.name = 'input'; input.value = mididata();
				const bu = newel('button'); bu.type = 'submit';
				bu.innerHTML = words_OldMethod();
				form.append(input, bu);
				form.style.display = 'inline-block';
				form.style.margin = '0.5em';
				return form;
			}
			return midiform();
		}
		let div = newel('fieldset');
		let leg = newel('legend');
		leg.innerText = words_Download() + ' ' + words_a_simplified_MIDI_file();
		let newbu = newel('button');
		newbu.innerText = words_NewMethod();
		newbu.onclick = function(){
			let notes = JSON.parse(wrapper.getAttribute('data-notes'));
			downloadMIDI(notes);
		};
		newbu.style.margin = '0.5em';
		div.append(leg, newbu, download_python_midi());
		return div;
	}

	let top_buttons = newel('div');
	top_buttons.style = 'display: flex; justify-content: space-between; width: 100%; margin-bottom: 1em;';
	top_buttons.append(Delete(), cancel);
	let bottom_buttons = newel('div');
	bottom_buttons.style = 'display: flex; justify-content: space-between; width: 100%; margin-top: 2em;';
	bottom_buttons.append(CloneButton(), save());
	let header = newel('div');
	header.innerText = words_PlaybackButtonEditor();
	header.style = "font-weight: bold; font-size: larger; text-align: center; margin-bottom: 1em;";
	
	editor.append(header, top_buttons, label(membu.innerHTML), DownloadAudio(), KeySensor(), Loop(), bottom_buttons);
	modal.append(editor);
	document.body.append(modal);
}

function spinButton(bu)
{
	function spinit()
	{
		bu.style.transition = "transform 1s ease-in-out";
		bu.style.transform = "rotate(360deg)";
		setTimeout(() => {
			bu.style.transition = ''; //"none";
			bu.style.transform = ''; //"rotate(0deg)";
		}, 1000);
	}
	setTimeout(spinit, 100);
}

function settings_icon()
{
	let svg = '<svg width="100%" height="100%" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M262.29,192.31a64,64,0,1,0,57.4,57.4A64.13,64.13,0,0,0,262.29,192.31ZM416.39,256a154.34,154.34,0,0,1-1.53,20.79l45.21,35.46A10.81,10.81,0,0,1,462.52,326l-42.77,74a10.81,10.81,0,0,1-13.14,4.59l-44.9-18.08a16.11,16.11,0,0,0-15.17,1.75A164.48,164.48,0,0,1,325,400.8a15.94,15.94,0,0,0-8.82,12.14l-6.73,47.89A11.08,11.08,0,0,1,298.77,470H213.23a11.11,11.11,0,0,1-10.69-8.87l-6.72-47.82a16.07,16.07,0,0,0-9-12.22,155.3,155.3,0,0,1-21.46-12.57,16,16,0,0,0-15.11-1.71l-44.89,18.07a10.81,10.81,0,0,1-13.14-4.58l-42.77-74a10.8,10.8,0,0,1,2.45-13.75l38.21-30a16.05,16.05,0,0,0,6-14.08c-.36-4.17-.58-8.33-.58-12.5s.21-8.27.58-12.35a16,16,0,0,0-6.07-13.94l-38.19-30A10.81,10.81,0,0,1,49.48,186l42.77-74a10.81,10.81,0,0,1,13.14-4.59l44.9,18.08a16.11,16.11,0,0,0,15.17-1.75A164.48,164.48,0,0,1,187,111.2a15.94,15.94,0,0,0,8.82-12.14l6.73-47.89A11.08,11.08,0,0,1,213.23,42h85.54a11.11,11.11,0,0,1,10.69,8.87l6.72,47.82a16.07,16.07,0,0,0,9,12.22,155.3,155.3,0,0,1,21.46,12.57,16,16,0,0,0,15.11,1.71l44.89-18.07a10.81,10.81,0,0,1,13.14,4.58l42.77,74a10.8,10.8,0,0,1-2.45,13.75l-38.21,30a16.05,16.05,0,0,0-6.05,14.08C416.17,247.67,416.39,251.83,416.39,256Z" style="fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>';
	let div = newel('div');
	div.classList.add('settings-icon');
	div.style.display = 'inline-block';
	div.style.width = '1.3em';
	div.style.height = '1.3em';
	div.innerHTML = svg;
	return div;
}
function download_icon()
{
	let div = newel('div');
	div.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" 
     width="24" height="24" viewBox="0 0 24 24" 
     fill="none" stroke="currentColor" stroke-width="2" 
     stroke-linecap="round" stroke-linejoin="round">
  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
  <polyline points="7 10 12 15 17 10"/>
  <line x1="12" y1="15" x2="12" y2="3"/>
</svg>
`;
	let svg = div.querySelector('svg');
	svg.style.display = 'inline-block';
	svg.style.width = '0.8em';
	svg.style.height = '0.8em';
	return svg;
}
function upload_icon()
{
	let div = newel('div');
	div.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
`;
	let svg = div.querySelector('svg');
	svg.style.display = 'inline-block';
	svg.style.width = '0.8em';
	svg.style.height = '0.8em';
	return svg;
}

function recognized_chords(midiNotes)
{
	function arraysEqual(a, b) { return a.length === b.length && a.every((val, i) => val === b[i]); }
	
	if (!midiNotes.length) return [];
	const pitchClasses = [...new Set(midiNotes.map(n => n % 12))].sort((a, b) => a - b);
	let rec = [];
	for (let root = 0; root < 12; root++) for (let chord of g_chords_database)
	{
		const transposed = chord.identity.map(i => (i + root) % 12).sort((a, b) => a - b);
		if (arraysEqual(pitchClasses, transposed)) rec.push({root: root, chord:chord});
	}
	return rec;
}

function recognized_chords_names(rec)
{
	function getNoteName(pc) {
		let noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
		if (g_lang === 'es') noteNames = ['Do', 'Do#', 'Re', 'Re#', 'Mi', 'Fa', 'Fa#', 'Sol', 'Sol#', 'La', 'La#', 'Si'];
		if (g_lang === 'ru') noteNames = ['До', 'До♯', 'Ре', 'Ре♯', 'Ми', 'Фа', 'Фа♯', 'Соль', 'Соль♯', 'Ля', 'Ля♯', 'Си'];
		return noteNames[pc % 12].replace('#', '&sharp;');
	}
	let ret = [];
	rec.forEach( function(x){
		if (x.chord.identity.join(',') === "0,3,6") return ret.push( getNoteName(x.root) + ' diminished triad voicing' );
		let chord_name = x.chord.name;
		if (g_lang === 'es') chord_name = x.chord.name_es;
		if (g_lang === 'ru') chord_name = x.chord.name_ru;
		chord_name =  chord_name.replace('7th', 'seventh').replace('9th', 'ninth').replace('11th', 'eleventh').replace('2nd', 'second').replace('4th', 'fourth');
		ret.push( getNoteName(x.root) + ' ' + chord_name );	
	});
	return ret;
}

function recognized_chord_name(midiNotes)
{
	return recognized_chords_names(recognized_chords(midiNotes)).map(x => '<span style="white-space:nowrap">'+x+'</span>').join(', ');
}
function chord_autonick(midiNotes)
{
	let noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
	let chords = recognized_chords(midiNotes);
	if (chords.length > 0) return noteNames[chords[0].root].replace('#','&sharp;') + chords[0].chord.display;
	
	function sound_name(n)
	{
		let letter = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B','B#'][n%12];
		letter = letter.replace('#', '&sharp;');
		let number = Math.floor(n/12)-1;
		return letter + number;
	}
	let name = '';
	midiNotes.forEach(n => name += sound_name(n) + ' ');
	return name;
}

function get_querySelecor_params(params, panel)
{
	function get_octaves(pianel)
	{
		let a=3, b=4;
		if (pianel.querySelector('#klawisz96')) a = 7;
		if (pianel.querySelector('#klawisz84')) a = 6;
		if (pianel.querySelector('#klawisz72')) a = 5;
		if (pianel.querySelector('#klawisz60')) a = 4;
		if (pianel.querySelector('#klawisz48')) a = 3;
		if (pianel.querySelector('#klawisz36')) a = 2;
		if (pianel.querySelector('#klawisz24')) a = 1; 
		if (pianel.querySelector('#klawisz36')) b = 2;
		if (pianel.querySelector('#klawisz48')) b = 3;
		if (pianel.querySelector('#klawisz60')) b = 4;
		if (pianel.querySelector('#klawisz72')) b = 5;
		if (pianel.querySelector('#klawisz84')) b = 6;
		if (pianel.querySelector('#klawisz96')) b = 7;
		if (pianel.querySelector('#klawisz108')) b = 8;
		return [a, b-1];
	}

	params.id = panel.id;
	[params.firstoctave, params.lastoctave] = get_octaves(panel);
	params.voice = g_voices[panel.querySelector('#voiceselector').selectedIndex].id;
	params.volume = parseInt(panel.querySelector('#volume_control').value);
	params.sustain = (panel.querySelector('#susbox').checked) ? 1 : 0;
	params.tab = panel.querySelector('#octaves_selector')?.selectedIndex || 3;
	params.notes = panel.querySelector('#notation_selector')?.value || default_note_names();
	params.layout = g_layouts[panel.querySelector('.id-layout-picker').selectedIndex].id;
	function get_marked_keys()
	{
		let marked = [];
		for (let n = 24; n <= 108; n++)
			if (panel.querySelector('#klawisz'+n+'.marked_key')) marked.push(n);
		return marked;
	}
	params.marked = get_marked_keys();
	function memorybutton_to_object(wrapper)
	{
		if (wrapper.classList.contains('chord-button-wrapper')) return chord_button_wrapper_to_object(wrapper);
		return playback_wrapper_to_object(wrapper);
	}
	params.memorybuttons = [...panel.querySelectorAll('.chord-button-wrapper,.playback-button-wrapper')].map(memorybutton_to_object);

	let chord_setter = panel.querySelector('#chord_setter');
	let [chord_data, delay_data] = chord_setter.innerText.split(' ');
	params.chord = chord_data.split(',').map(x => parseInt(x));
	params.delay = parseInt(delay_data);
	// nothing to return, params has been modified
}

function JSON_from_all_pianels()
{
	function get_params(panel)
	{
		let params = { shape: 3 };
		get_querySelecor_params(params, panel);
		return params;
	}
	let params_array = [];
	document.querySelectorAll('.piano-panel').forEach(x => params_array.push(get_params(x)));
	return JSON.stringify({ pianopanels: params_array }, null, 1);
}

function download_save_all()
{
	function filename()
	{
		const now = new Date();
		const year = now.getFullYear();
		const month = String(now.getMonth() + 1).padStart(2, '0');
		const day = String(now.getDate()).padStart(2, '0');
		const hours = String(now.getHours()).padStart(2, '0');
		const minutes = String(now.getMinutes()).padStart(2, '0');
		const seconds = String(now.getSeconds()).padStart(2, '0');
		let time = `${year}${month}${day}-${hours}${minutes}${seconds}`;
		return 'VirtualPiano.Online-' + time + '.html';
	}
	let jsonString = JSON_from_all_pianels().replaceAll("</textarea>", "</text'+'area>");
	let targetUrl = "https://virtualpiano.online";
	if (g_lang === 'es') targetUrl = "https://virtualpiano.online/teclado/";
	if (g_lang === 'ru') targetUrl = "https://virtualpiano.online/pianino/";
	let pageTitle = 'Saved from VirtualPiano.Online';
	let autoSubmit = true;
	let htmlContent = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>${pageTitle}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<form method="POST" action="${targetUrl}" accept-charset="UTF-8">
<button type="submit">Open at VirtualPiano.Online</button>
<hr>
<textarea style="width: 100%; height:90vh" name="virtualpianojson">${jsonString}</textarea>
</form>
<script>
${autoSubmit ? "document.querySelector('form').submit();" : ""}
</script>
</body>
</html>`;
	let blob = new Blob([htmlContent], { type: "text/html" });
	let a = newel("a");
	a.href = URL.createObjectURL(blob);
	a.download = filename();
	a.click();
	URL.revokeObjectURL(a.href);
}

let g_recordTracker = null;

function post_init(where)
{
	g_recordTracker = initRecordingTracker(where);
	
	document.body.addEventListener('click', function(e) {
		if (e.target.closest('.piano-panel') === null) g_mini_mode_on = true;
	});
	
	document.body.addEventListener('keydown', function(e) {
		if (e.code === 'Escape') enforceSilence();
	});
	
	function horizontal_focus()
	{
		document.querySelector('.klawa')?.scrollIntoView(false);
	}
	try
	{
		PianoOrientation.onPortraitToLandscapePhone(horizontal_focus);
	}
	catch(e) {};
}
function pre_init(pianopanels)
{
	let no_need_for_major_scale_layouts = true;
	pianopanels.forEach(function(x) {
		if (x.layout.includes('major_scale_'))
			no_need_for_major_scale_layouts = false;
	});
	if (!no_need_for_major_scale_layouts)
		g_layouts = [...g_layouts, ...major_scale_arpeggion_layouts()];	
}
function init_pianopanels(where)
{
	let pianopanels = g_postdata?.pianopanels || [];
	if (pianopanels.length === 0)
	{
		let params = default_pianopanel_params();
		let pianel = pianopanel(params);
		let label = recognized_chord_name(params.marked);
		if (label)
		{
			let chord_label = pianel.querySelector('#chord-label');
			chord_label.style.display = '';
			chord_label.innerHTML = label;
		}
		where.append(pianel);
	}
	else
	{
		pre_init(pianopanels);
		pianopanels.forEach(x => where.append(pianopanel(x)));
	}
	post_init(where);
}

function initRecordingTracker(where)
{
	let anyRecording = false;
  const ROOT = where;
  const REC_SELECTOR = '.piano-panel input.recordbox:checked';

  const recomputeRecordingState = () => {
    anyRecording = !!ROOT.querySelector(REC_SELECTOR);
  };

  // Event handlers we may later remove
  const onChange = (e) => {
    const t = e.target;
    if (t instanceof HTMLInputElement && t.matches(REC_SELECTOR.replace(':checked', ''))) {
      recomputeRecordingState();
    }
  };

  // Observe adds/removals (clones/deletes)
  const observer = new MutationObserver(() => {
    recomputeRecordingState();
  });
  observer.observe(ROOT, { childList: true, subtree: true });

  // Listen to changes (capture for reliability across shadowy trees)
  ROOT.addEventListener('change', onChange, true);

  // Initial compute
  recomputeRecordingState();

  // Public, cheap API for your hot path
  return {
    isAnyRecording: () => anyRecording,
    recompute: recomputeRecordingState,   // call if you flip .checked in code and skip events
    teardown() {                          // clean up if the root is removed/replaced
      observer.disconnect();
      ROOT.removeEventListener('change', onChange, true);
    }
  };
}


function loadApronusButton()
{
	async function readApronusFile(evt)
	{
		const input = evt.target;
		const file = input?.files?.[0];
		if (!file) { alert('Failed to load file'); return; }
		try { process_file_text(await file.text()); }
		catch (err) { console.error(err); alert('Could not read file'); }
		finally { input.value = ''; }
	}

	function process_file_text(txt)
	{
		let where = el('virtual-piano-widget');

		if (txt.includes('<!DOCTYPE html>'))
		{
			let div = newel('div');
			div.innerHTML = txt.split('<body>')[1].split('</body>')[0];
			let json = JSON.parse(div.querySelector('textarea').value);
			
			if (json.pianopanels)
			{
				where.innerHTML = '';
				pre_init(json.pianopanels);
				json.pianopanels.forEach(x => where.append(pianopanel(x)));
				where.scrollIntoView();
			}
			return;
		}
		
		let params = default_pianopanel_params();
		params.memorybuttons = parse_apronus(txt);
		where.innerHTML = '';
		where.append(pianopanel(params));
		where.scrollIntoView();
	}

	function parse_apronus(txt)
	{
		let parsicle = document.createElement('div');
		parsicle.innerHTML = txt;
		
		let arr = [];
		let items = parsicle.querySelectorAll('table');
		for (let item of items)
		{
			if (!item.querySelector('button.chord,button.playback')) continue;
			let obj = { };
			
			let bu = item.querySelector('button.chord,button.playback');
			obj.label = bu.innerHTML.replaceAll(" class=\"noselect\" style=\"user-select: none;\"", '');
			if (bu.classList.contains('chord'))
			{	
				obj.type = 'chord';
				obj.playback = 0;
				let notes = bu.getAttribute('onmousedown').split('activatepianokey');
				notes[0] = '';
				obj.notes = notes.join('').replaceAll(');(', ',').replace('(', '').replace(');', '').split(',');
			}
			else
			{
				obj.type = 'playback';
				let parsenotes = bu.getAttribute('onmouseup').split('playpianosound(audiocontext,');
				let notes = [];
				for (let i = 1; i < parsenotes.length; i++)
				{
					let [n, delay] = parsenotes[i].split(',');
					notes.push({
						n: parseInt(n),
						delay: parseFloat(delay) * 1000
					});
				}
				obj.notes = notes;
			}
			let keysensor2025 = item.querySelector('input.ks2025');
			let ks = keysensor2025?.getAttribute('name').replace('a','') || '';
			obj.keysensor = ks + ',0,0,0';
			
			if (item.parentElement.style.position === 'absolute')
			{
				obj.position = { x: item.parentElement.style.left, y: item.parentElement.style.top };
			}
			arr.push(obj);
		}
		return arr;
	}

	const label = document.createElement('label');
	label.innerHTML = words_LoadApronus();
	const input = document.createElement('input');
	input.type = 'file';
	input.accept = '.txt,.html';
	input.hidden = true;
	input.addEventListener('change', readApronusFile, false);
	label.appendChild(input);
	label.style.background = '#eee';
	label.style.border = '1px solid black';
	label.style.padding = '0.5em';
	label.style.borderRadius = '0.5em';
	label.style.cursor = 'pointer';
	if (g_isDark)
	{
		label.style.background = '#555';
		label.style.color = '#eee';
	}
	return label;
}
function importButton()
// this is an improved version of the above function loadApronusButton()
// it can append rather than replace
// it also labels the button Import plus the download icon
{
	async function readApronusFile(evt)
	{
		const input = evt.target;
		const file = input?.files?.[0];
		if (!file) { alert('Failed to load file'); return; }
		try { process_file_text(await file.text()); }
		catch (err) { console.error(err); alert('Could not read file'); }
		finally { input.value = ''; }
	}

	function process_file_text(txt)
	{
		let where = el('virtual-piano-widget');
		
		function input_pianopanels_array()
		{
			if (txt.includes('<!DOCTYPE html>'))
			{
				let div = newel('div');
				div.innerHTML = txt.split('<body>')[1].split('</body>')[0];
				let json = JSON.parse(div.querySelector('textarea').value);
				return json.pianopanels || [];
			}
			let params = default_pianopanel_params();
			params.memorybuttons = parse_apronus(txt);
			return [params];
		}
		function replaceOrAppend()
		{
			for (let params of JSON.parse(JSON_from_all_pianels()).pianopanels)
			{
				if (params.memorybuttons.length > 0) return 'append';
				if (params.chord.length > 1) return 'append';
			}
			return 'replace';
		}
		
		if (replaceOrAppend() === 'replace') where.innerHTML = '';
		
		let arr = input_pianopanels_array();
		
		pre_init(arr);
		arr.forEach(x => where.append(pianopanel(x)));
		where.scrollIntoView();
	}

	function parse_apronus(txt)
	{
		let parsicle = document.createElement('div');
		parsicle.innerHTML = txt;
		
		let arr = [];
		let items = parsicle.querySelectorAll('table');
		for (let item of items)
		{
			if (!item.querySelector('button.chord,button.playback')) continue;
			let obj = { };
			
			let bu = item.querySelector('button.chord,button.playback');
			obj.label = bu.innerHTML.replaceAll(" class=\"noselect\" style=\"user-select: none;\"", '');
			if (bu.classList.contains('chord'))
			{	
				obj.type = 'chord';
				obj.playback = 0;
				let notes = bu.getAttribute('onmousedown').split('activatepianokey');
				notes[0] = '';
				obj.notes = notes.join('').replaceAll(');(', ',').replace('(', '').replace(');', '').split(',');
			}
			else
			{
				obj.type = 'playback';
				let parsenotes = bu.getAttribute('onmouseup').split('playpianosound(audiocontext,');
				let notes = [];
				for (let i = 1; i < parsenotes.length; i++)
				{
					let [n, delay] = parsenotes[i].split(',');
					notes.push({
						n: parseInt(n),
						delay: parseFloat(delay) * 1000
					});
				}
				obj.notes = notes;
			}
			let keysensor2025 = item.querySelector('input.ks2025');
			let ks = keysensor2025?.getAttribute('name').replace('a','') || '';
			obj.keysensor = ks + ',0,0,0';
			
			if (item.parentElement.style.position === 'absolute')
			{
				obj.position = { x: item.parentElement.style.left, y: item.parentElement.style.top };
			}
			arr.push(obj);
		}
		return arr;
	}

	const label = document.createElement('label');
	label.append(words_Import() + ' ', upload_icon());
	const input = document.createElement('input');
	input.type = 'file';
	input.accept = '.txt,.html';
	input.hidden = true;
	input.addEventListener('change', readApronusFile, false);
	label.appendChild(input);
	label.classList.add('ostylowany');
	return label;
}

function bookmark_chords_query(pianel)
{
	let chord_buttons = pianel.querySelectorAll('.chord-button-wrapper');
	if (chord_buttons.length === 0) return null;
	let chords = [];
	chord_buttons.forEach(x => chords.push(chord_button_wrapper_to_object(x).notes.join('-')));
	return '?c=' + chords.join('_');
}

function sound_name(n)
{
	function letter(pc) {
		let noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
		if (g_lang === 'es') noteNames = ['Do', 'Do#', 'Re', 'Re#', 'Mi', 'Fa', 'Fa#', 'Sol', 'Sol#', 'La', 'La#', 'Si'];
		if (g_lang === 'ru') noteNames = ['До', 'До♯', 'Ре', 'Ре♯', 'Ми', 'Фа', 'Фа♯', 'Соль', 'Соль♯', 'Ля', 'Ля♯', 'Си'];
		return noteNames[pc % 12].replace('#', '&sharp;');
	}
	let number = Math.floor(n/12)-1;
	return letter(n) + number;
}

function enforceSilence()
{
	resetAudioContext();
	for (let wrapper of document.querySelectorAll('.playback-button-wrapper'))
	{
		let new_wrapper = playback_button_wrapper(playback_wrapper_to_object(wrapper));
		wrapper.replaceWith(new_wrapper);
	}
};"use strict";

(function (global) {
  // ====== Config: hard-wired grid (edit here) ======
  const GRID_X = 10;
  const GRID_Y = 10;

  // ====== Internal state ======
  const registry = new WeakMap();      // el -> { options, startRect, offsetX, offsetY }
  let active = null;                   // { el, options, fromContainer, startRect, offsetX, offsetY }
  let installed = false;
  const lastOverPoint = new WeakMap(); // Map<containerEl, {x,y}>

  // ====== Utils ======
  const clamp = (n, min, max) => Math.max(min, Math.min(n, max));
  const snap  = (n, step) => (step ? Math.round(n / step) * step : n);

  function computeDropCoords(container, clientX, clientY, elW, elH, offsetX, offsetY) {
    const cRect = container.getBoundingClientRect();
    let left = clientX - offsetX - cRect.left + container.scrollLeft;
    let top  = clientY - offsetY - cRect.top  + container.scrollTop;

    const maxLeft = container.clientWidth  - elW;
    const maxTop  = container.clientHeight - elH;

    left = clamp(left, 0, Math.max(0, maxLeft));
    top  = clamp(top,  0, Math.max(0, maxTop));

    left = snap(left, GRID_X);
    top  = snap(top,  GRID_Y);

    left = clamp(left, 0, Math.max(0, maxLeft));
    top  = clamp(top,  0, Math.max(0, maxTop));
    return { left, top };
  }

  // ====== Global listeners (installed once) ======
  function installDocListeners() {
    if (installed) return;
    installed = true;

    document.addEventListener('dragover', (ev) => {
      if (!active) return;
      const cls = '.' + active.options.containerClass;
      const container = ev.target.closest?.(cls);
      if (!container) return;

      ev.preventDefault(); // allow drop here
      try { ev.dataTransfer.dropEffect = 'move'; } catch {}
      lastOverPoint.set(container, { x: ev.clientX, y: ev.clientY });
    });

    document.addEventListener('drop', (ev) => {
      if (!active) return;

      const { el, options, fromContainer, startRect, offsetX, offsetY } = active;
      const cls = '.' + options.containerClass;
      const toContainer = ev.target.closest?.(cls);
      active = null; // stop routing further events to this drag

      if (!toContainer) return;
      ev.preventDefault();

      const elW = startRect?.width ?? el.offsetWidth;
      const elH = startRect?.height ?? el.offsetHeight;
      const pt = lastOverPoint.get(toContainer) || { x: ev.clientX, y: ev.clientY };
      const { left, top } = computeDropCoords(toContainer, pt.x, pt.y, elW, elH, offsetX, offsetY);

      // INTERNAL: drop back into same container (and there *was* a container at drag start)
      if (fromContainer && toContainer === fromContainer) {
        if (el.parentNode !== toContainer) toContainer.appendChild(el);
        el.style.position = 'absolute';
        el.style.left = left + 'px';
        el.style.top  = top  + 'px';
        el.style.margin = '0';
        return;
      }

      // EXTERNAL: (either different container or no home at drag start)
      const clone = options.cloneFactory(el, toContainer);
      if (!(clone instanceof HTMLElement)) return; // user returned nothing/invalid

      clone.style.position = 'absolute';
      clone.style.left = left + 'px';
      clone.style.top  = top  + 'px';
      clone.style.margin = '0';
      toContainer.appendChild(clone);

      // Automatically make the clone draggable with the same options;
      // home container will be derived on its future drags as well.
      makeDraggable(clone, options);
    });

    // Safety: clear routing even if drop lands outside any listener
    document.addEventListener('dragend', () => { active = null; });
  }

  // ====== Public API ======
  function makeDraggable(el, {
    containerClass,
    cloneFactory, // function (sourceEl) => HTMLElement
  } = {}) {
    if (!el) throw new Error('makeDraggable: el is required');
    if (!containerClass) throw new Error('makeDraggable: options.containerClass is required');
    if (typeof cloneFactory !== 'function') throw new Error('makeDraggable: options.cloneFactory must be a function');

    installDocListeners();

    const options = { containerClass, cloneFactory };
    registry.set(el, { options, startRect: null, offsetX: 0, offsetY: 0 });

    function onMouseDown(ev) {
      const r = el.getBoundingClientRect();
      const state = registry.get(el);
      if (!state) return;
      state.startRect = r;
      state.offsetX = ev.clientX - r.left;
      state.offsetY = ev.clientY - r.top;
    }

    function onDragStart(ev) {
      const state = registry.get(el);
      if (!state) return;

      // derive "home" container at drag start (may be null)
      const from = el.closest?.('.' + options.containerClass) || null;

      active = {
        el,
        options,               // pass same options so clones use same containerClass/cloneFactory
        fromContainer: from,
        startRect: state.startRect,
        offsetX: state.offsetX,
        offsetY: state.offsetY,
      };

      try {
        ev.dataTransfer.effectAllowed = 'move';
        ev.dataTransfer.setDragImage(el, state.offsetX, state.offsetY);
      } catch {}
    }

    el.draggable = true;
    el.addEventListener('mousedown', onMouseDown);
    el.addEventListener('dragstart', onDragStart);

    // optional teardown
    return function teardown() {
      el.draggable = false;
      el.removeEventListener('mousedown', onMouseDown);
      el.removeEventListener('dragstart', onDragStart);
      registry.delete(el);
    };
  }

  // expose via your bucket
  global.g_drag_and_drop = global.g_drag_and_drop || {};
  global.g_drag_and_drop.makeDraggable = makeDraggable;

})(window);



function makeDraggable(elem)
{
	let params = {
		containerClass: 'memorybuttons',
		cloneFactory: function(wrapper, toContainer) {
			if (wrapper.classList.contains('chord-button-wrapper'))
				return chord_button_wrapper(chord_button_wrapper_to_object(wrapper));
			let old_id = wrapper.closest('.piano-panel').id;
			let new_id = toContainer.closest('.piano-panel').id;
			let newJSON = JSON.stringify(playback_wrapper_to_object(wrapper)).replaceAll(`"${old_id}"`,`"${new_id}"`);
			return playback_button_wrapper(JSON.parse(newJSON));
		}
	};
	g_drag_and_drop.makeDraggable(elem, params);
}
"use strict";

function downloadMIDI(notes)
{

/* ====== Tiny MIDI (Format 0) with ms timing ======
   - One track, channel 0, program 0 (Acoustic Grand)
   - PPQ = 480, Tempo = 480,000 µs/qn  => 1 tick = 1 ms
   - Input notes: [{ t: msStart, n: midiNote, d?: msDur }]
   - If d is omitted, DEFAULT_DUR_MS is used.
*/

// ---- Helpers
function vlq(n) {               // MIDI variable-length quantity
  const out = [];
  do { out.unshift(n & 0x7F); n >>>= 7; } while (n > 0);
  for (let i = 0; i < out.length - 1; i++) out[i] |= 0x80;
  return out;
}
function be16(n){ return [(n>>>8)&255, n&255]; }
function be32(n){ return [(n>>>24)&255,(n>>>16)&255,(n>>>8)&255,n&255]; }

// ---- Core
function makeMIDI({
  notes,
  defaultDurMs = 220,   // default length if d is missing
  pedalHoldMs  = 900,   // keep pedal after last note-off
  tailMs       = 700    // silent tail after pedal up
}) {
  // Header: 'MThd', len=6, format=0, tracks=1, division=480
  const header = [
    0x4D,0x54,0x68,0x64, 0x00,0x00,0x00,0x06,
    0x00,0x00, 0x00,0x01, ...be16(480)
  ];

  const track = [];
  let lastTime = 0;
  const push = (absMs, bytes) => {
    const dt = Math.max(0, absMs - lastTime);
    lastTime = absMs;
    track.push(...vlq(dt), ...bytes);
  };

  // Tempo: 480,000 µs/qn (1 ms per tick at PPQ=480)
  push(0, [0xFF,0x51,0x03, 0x07,0x53,0x00]);

  // Program Change: channel 0 -> Acoustic Grand (0)
  push(0, [0xC0 | 0, 0x00]);

  // Global volume for the channel (0–127). Try 80–110.
  push(0, [0xB0 | 0, 7, 127]); // CC7 = Channel Volume

  // Sustain pedal ON (CC64 = 127) at start
  push(0, [0xB0 | 0, 64, 127]);

  // Expand into on/off events
  const evts = [];
  for (const x of notes) {
    const t = Math.max(0, x.t|0);
    const d = (x.d == null ? defaultDurMs : x.d)|0;
    const n = x.n & 0x7F;
    evts.push({ t,                type:'on',  n });
    evts.push({ t: t + Math.max(0, d), type:'off', n });
  }

  // Sort by time; at same time, Note OFF before Note ON
  evts.sort((a,b) => a.t - b.t || (a.type === 'on') - (b.type === 'on'));

  // Emit notes (fixed velocities)
  for (const e of evts) {
    if (e.type === 'on')  push(e.t, [0x90 | 0, e.n, 100]);
    else                  push(e.t, [0x80 | 0, e.n,  64]);
  }

  // Find the time of the final NOTE OFF (not just any event)
  const lastOffTime = evts.reduce((m,e)=> e.type==='off' ? Math.max(m,e.t) : m, 0);

  // Keep pedal down for a while AFTER the last note-off, then lift
  const pedalOffAt = lastOffTime + Math.max(0, pedalHoldMs);
  push(pedalOffAt, [0xB0 | 0, 64, 0]); // CC64 OFF

  // Add a quiet tail so release finishes before the track actually ends
  const endAt = pedalOffAt + Math.max(0, tailMs);
  push(endAt, [0xFF,0x2F,0x00]); // End of Track

  // Wrap as 'MTrk'
  const trackChunk = [0x4D,0x54,0x72,0x6B, ...be32(track.length), ...track];
  return new Blob([new Uint8Array([...header, ...trackChunk])], { type: 'audio/midi' });
}

function downloadSimpleMidi(notes, filename) {
  // Example: C4 at 0ms (250ms), E4 at 300ms (200ms), G4 at 600ms (300ms)
  const blob = makeMIDI({ notes, defaultDurMs: 200 });
  const url = URL.createObjectURL(blob);
  const a = Object.assign(document.createElement('a'), { href: url, download: filename });
  document.body.appendChild(a);
  a.click(); a.remove();
  URL.revokeObjectURL(url);
}

function process_my_notes(notes)
{
	let arr = [];
	for (let note of notes) arr.push( { n: note.n, t: note.delay } );
	return arr;
}

downloadSimpleMidi(process_my_notes(notes), 'apronus-' + Date.now().toString(36) + '.mid');

}

/*
  const notes = [
    { t:   0, n:60, d:250 },
    { t: 300, n:64, d:200 },
    { t: 600, n:67, d:300 }
  ];
*/"use strict";

function widget(chord, delay, numbers, a, b)
{
	function isblackkey(n)
	{
		let i = n % 12;
		return (i==1 || i==3 || i==6 || i==8 || i==10);
	}
	function soundletter(n,sharp)
	{
		let names = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B','Cb'];
		if (sharp) names = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B','B#']; 
		return names[n%12].replace('#','&sharp;').replace('b','&flat;');
	}
	function soundnumber(n)
	{
		return Math.floor(n/12)-1;
	}
	function soundname(n,sharp)
	{
		return soundletter(n,sharp) + soundnumber(n,sharp);
	}

	let widget = newel('div');
	
	function widget_button_down(n)
	{
		const button_delay = 100;
		
		widget.querySelectorAll('button').forEach((bu) => bu.style.outline = 'none');
		for (let i = 0; i < chord.length; i++)
		{
			let x = chord[i];
			let bu = widget.querySelector('button#button' + (n+x));
			bu.style.outline = 'thick solid orange';
			setTimeout(function(){ bu.style.transform = 'translateY(6px)'; }, i*delay);
			setTimeout(function(){ bu.style.transform = "none" }, button_delay + i*delay);
			g_voices[0].play(n+x, i*delay/1000, 2, true);
		}
	}

	function button(n, numberit, sharp)
	{
		let bu = newel('button');
		bu.id = 'button'+n;
		//bu.style.touchAction = 'none';
		bu.style.userSelect = 'none';
		bu.style.cursor = 'pointer';
		bu.innerHTML = soundname(n,sharp);
		function fits()
		{
			for (let x of chord)
			{
				if (n+x > b || n+x < a) return false;
			}
			return true;
		}
		if (fits())
			bu.addEventListener("pointerdown", function() { widget_button_down(n); }, { passive: false });
		else
			bu.style.cursor = 'not-allowed';

		let number = newel('div');
		number.className = 'soundnumber';
		number.innerHTML = n;
		number.style.color = isblackkey(n) ? 'lightgray' : 'gray';
		number.style.marginTop = '7px';
		number.style.fontSize = 'smaller';
		if (numberit) bu.append(number);
		
		bu.style.fontFamily = 'Arial';
		bu.style.outline = 'none'; bu.style.padding = '0'; bu.style.margin = '0';
		bu.style.width = '32px';
		bu.style.height = (numberit) ? '48px' : '32px';
		bu.style.border = '1px solid gray';
		if (isblackkey(n)) { bu.style.backgroundColor = 'black'; bu.style.color = 'white'; }
		else { bu.style.backgroundColor = 'white'; bu.style.color = 'black'; }
		return bu;
	}

	let tr = newel('tr');
	for (let i=a; i<=b; i++)
	{
		let td = newel('td'); td.style.padding = '0'; td.style.border = '0';
		let sharp = (i%24) < 12;
		td.append(button(i, numbers, sharp));
		tr.append(td);
	}
	let table = newel('table');
	table.style.margin = '0.5em auto 1em auto';
	table.style.borderCollapse = 'separate';
	table.style.borderSpacing = '4px';
	table.appendChild(tr);
	//touchActionNone_for_button_containers(table);
	widget.append( table );
	widget.style.overflowY = 'auto';
	return widget;
}
"use strict";

function newel(el) { return document.createElement(el); }
function el(id) { return document.getElementById(id); }

function form_widget()
{
	function root_selector()
	{
		function isblackkey(n)
		{
			let i = n % 12;
			return (i==1 || i==3 || i==6 || i==8 || i==10);
		}
		let rootselector = newel('div');
		rootselector.id = 'rootselector';
		let names = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B'];
		//names = ['C','C#/Db','D','D#/Eb','E','F','F#/Gb','G','G#/Ab','A','A#/Bb','B'];
		for (let i=0; i<=11; i++)
		{
			let r = newel('input'); r.type = 'radio'; r.name = 'pitchclass';
			r.id = 'pitchclass'+i;
			r.value = i;
			let l = newel('label');
			l.style.display = 'inline-block';
			l.style.border = '1px solid gray';
			l.style.padding = '0';
			l.style.padding = '0.3em';
			if (isblackkey(i))
			{
				l.style.background = 'black';
				l.style.color = 'white';
			}
			else
			{
				l.style.background = 'white';
				l.style.color = 'black';
			}
			l.innerHTML = names[i].replace('#', '&sharp;').replace('b', '&flat;');
			l.append(r);
			if (i==0) r.checked = true;
			rootselector.append(l);
		}
		rootselector.style.marginBottom = '1em';
		return rootselector;
	}
	function ilenotes()
	{
		let div = newel('div');
		div.id = 'ilenotes';
		for (let i = 3; i <= 6; i++)
		{
			let label = newel('label');
			let input = newel('input');
			input.type = 'checkbox';
			input.id = 'notes' + i;
			if (i < chord.length) label.style.display = 'none';
			if (i === chord.length) input.checked = true;
			label.append(input, i + ' notes');
			label.style.margin = '0 1em 0 1em';
			div.append(label);
		}
		return div;
	}
	let formu = newel('form');
	formu.append(root_selector(), ilenotes());
	formu.onclick = updatechordbuttons;
	formu.addEventListener('click', function(){
		el('virtual-piano-widget').querySelectorAll('.whitekeybutton,.blackkeybutton').forEach(x => x.classList.remove('marked_key'));
	});
	return formu;
}

function read_query()
{
	const params = new URLSearchParams(window.location.search);
	
	let root = params.get('root') || 'C';
	let names = ['C','Cs','D','Ds','E','F','Fs','G','Gs','A','As','B'];
	let i = names.indexOf(root);
	if (i === -1)
	{
		names = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B'];
		i = names.indexOf(root);
		if (i === -1) i = 0;
	}
	document.querySelector('#pitchclass' + i).checked = true;
	
	let notes = params.get('notes') || (''+chord.length);
	el('notes3').checked = notes.includes('3');
	el('notes4').checked = notes.includes('4');
	el('notes5').checked = notes.includes('5');
	el('notes6').checked = notes.includes('6');
}
function get_root()
{
	return parseInt(el('rootselector').querySelector('input:checked').value);
}
function get_range()
{
	let first_pianel = document.querySelector('.piano-panel');
	function key(n) { return !!first_pianel.querySelector('#klawisz' + n); }
	let a=3, b=4;
	if (key(96)) a = 7;
	if (key(84)) a = 6;
	if (key(72)) a = 5;
	if (key(60)) a = 4;
	if (key(48)) a = 3;
	if (key(36)) a = 2;
	if (key(24)) a = 1; 
	if (key(36)) b = 2;
	if (key(48)) b = 3;
	if (key(60)) b = 4;
	if (key(72)) b = 5;
	if (key(84)) b = 6;
	if (key(96)) b = 7;
	if (key(108)) b = 8;
	return [a, b];	
}
function generate_query()
{
	let names = ['C','Cs','D','Ds','E','F','Fs','G','Gs','A','As','B'];
	let root = names[get_root()];
	let notes = '';
	for (let i = chord.length; i <= 6; i++)
	{
		notes += (el('notes'+i)?.checked) ? i : '';
	}
	
	notes = (notes == chord.length) ? '' : ('&notes=' + notes);
	let [a, b] = get_range();
	let range = '&range=C' + a + 'C' + b;
	if (range === '&range=C3C5') range = '';
	return '?root=' + root + notes + range;
}
function update_bookmark()
{
	let URL = g_page_url;
	URL += generate_query();
	el('bookmarkURL').href = URL;
	el('bookmarkURL').innerText = URL;
}

function generate_chords()
{
	function chord_array_from_pitch_classes(a,b,chromas)
	{
		const length = chromas.length;
		if (length == 0) return [];
		if (b-a+1 < length) return [];
		let ret = [];
		for (let n=a; n<=b; n++)
		{
			let index = chromas.indexOf(n%12);
			if (index > -1)
			{
				let head = [n];
				if (length == 1)
				{
					ret.push(head);
				}
				else
				{
					let smaller = [];
					for (let i=0; i < length; i++) if (i != index) smaller.push(chromas[i]);
					let tails = chord_array_from_pitch_classes(n+1,b,smaller);
					for (let i=0; i < tails.length; i++)
					{
						let chord = head.concat(tails[i]);
						if (chord.length == length) ret.push(chord);
					}
				}
			}
		}
		return ret;
	}

	function return_arr(arr)
	{
		let set = new Set(arr.map(x => JSON.stringify(x)));
		function span(chord)
		{
			let span = 0;
			for (let i = 0; i < chord.length - 1; i++)
				span = Math.max(span, chord[i+1] - chord[i]);
			return span;
		}
		function sort(a, b)
		{
			if (a.length < b.length) return -1;
			if (a.length > b.length) return 1;
			let spana = span(a), spanb = span(b);
			if (spana < spanb) return -1;
			if (spana > spanb) return 1;
			if (JSON.stringify(a) < JSON.stringify(b)) return -1;
			return 1;
		}
		return [...set].map(x => JSON.parse(x)).sort(sort);
	}

	let [a, b] = get_range();
	a = a*12+12; b = b*12+12;

	let root = get_root();

	let arr = [];

	function grow(chromas)
	{
		arr = arr.concat(chord_array_from_pitch_classes(a, b, chromas));
	}

	function generate_triad_chords()
	{		
		let chromas3 = []; chord.forEach(x => chromas3.push((root+x)%12));
		
		if (el('notes3')?.checked) grow([].concat(chromas3));
		
		let chromas4 = [];
		for (let x of chord)
		{
			let chromas = [(root+x)%12].concat(chromas3);
			chromas4.push(chromas);
			if (el('notes4')?.checked) grow(chromas);
		}

		let chromas5 = [];
		for (let x of chord) for (let chr of chromas4)
		{
			let chromas = [(root+x)%12].concat(chr);
			chromas5.push(chromas);
			if (el('notes5')?.checked) grow(chromas);
		}

		if (el('notes6')?.checked)
			for (let x of chord) for (let chr of chromas5)
				grow([(root+x)%12].concat(chr));
	}

	function generate_4note_chords()
	{		
		let chromas4 = []; chord.forEach(x => chromas4.push((root+x)%12));
		
		if (el('notes4')?.checked) grow([].concat(chromas4));
		
		let chromas5 = [];
		for (let x of chord)
		{
			let chromas = [(root+x)%12].concat(chromas4);
			chromas5.push(chromas);
			if (el('notes5')?.checked) grow(chromas);
		}

		if (el('notes6')?.checked)
			for (let x of chord) for (let chr of chromas5)
				grow([(root+x)%12].concat(chr));
	}

	if (chord.length === 3) generate_triad_chords();
	if (chord.length === 4) generate_4note_chords();
	
	return return_arr(arr);
}

function updatechordbuttons()
{
	el('memorybuttons').innerHTML = '';
	
	let chords = generate_chords();
	let root = get_root();
	let sharp_names = ['C','Cs','D','Ds','E','F','Fs','G','Gs','A','As','B'];
	let flat_names = ['C','Db','D','Eb','E','F','Gb','G','Ab','A','Bb','B'];
	let sharp_name = sharp_names[root].replace('s', '&sharp;');
	let flat_name = flat_names[root].replace('b', '&flat;');
	let root_name = (sharp_name === flat_name) ? sharp_name : (sharp_name + ' / ' + flat_name);
	let chord_type = g_chord_type;
	let chord_name = root_name + ' ' + chord_type + ' chord';
	el('chords_title').innerHTML = chords.length + ' voicings of the <b>' + chord_name + '</b>';

	chords.forEach(function(notes){
		let x = {};
		x.pianel = el('virtual-piano-widget');
		x.label = null;
		x.notes = notes;
		el('memorybuttons').append(chords_demo_button(x));
	});
}

function pianel()
{
	let params = default_pianopanel_params();
	let range = (new URLSearchParams(window.location.search)).get('range') || '';
	let a = 3, b = 5;
	if (range.length === 4)
	{
		a = parseInt(range[1]);
		b = parseInt(range[3]);
		if (isNaN(a)) a = 3;
		if (isNaN(b)) b = 5;
		if (a >= b || a < 1 || b > 8) { a = 3; b = 5; }
	}
	params.firstoctave = a;
	params.lastoctave = b - 1;
	params.volume = '3';
	params.shape = params.isMobile ? 0 : 1;
	params.chord = chord;
	let root = get_root();
	params.marked = JSON.stringify([].concat(chord).map(x => x + (a+1)*12 + root));
	let playmode = el('virtual-piano-widget').querySelector('input[name="playmode"]:checked').value;
	if (playmode !== 'together') params.delay = 150 / (chord.length - 1);
	if (playmode === 'descending') params.chord = params.chord.reverse();
	let pianel = pianopanel(params);
	let div = newel('div');
	div.append(pianel);
	div.addEventListener('click', function(e) {
		if (e.target.classList.contains('range_change_button'))
		{
			updatechordbuttons();
			update_bookmark();
		}
	});
	return div;
}

function update_playmode_chords(notes)
{
	let pianel = el('virtual-piano-widget');
	if (notes === undefined)
	{
		let thispianel = pianel.querySelector('.piano-panel');
		notes = [];
		thispianel.querySelectorAll('.marked_key').forEach( key => notes.push(parseInt(key.id.replace('klawisz',''))));
		notes.sort();
	}
	let chord = [];
	notes.forEach( note => chord.push(note - notes[0]) );
	let playmode = pianel.querySelector('input[name="playmode"]:checked')?.value;
	if (playmode === 'descending') chord = chord.reverse();
	let delay = Math.floor(150 / (notes.length - 1));
	if (playmode === 'together') delay = 0;
	pianel.querySelector('#chord_setter').innerText = chord + ' ' + delay;
	pianel.querySelector('#chord_setter').click();
}

function chords_demo_button(x)
{
	function sound_name(n)
	{
		let letter = ['C','C#','D','D#','E','F','F#','G','G#','A','A#','B','B#'][n%12];
		letter.replace('#', '&sharp;');
		let number = Math.floor(n/12)-1;
		return letter + number + ' ';
	}
	function chord_name()
	{
		let name = '';
		x.notes.forEach(n => name += sound_name(n));
		return name;
	}
	if (!x.label) x.label = chord_name();
	
	let pianel = x.pianel;
	let bu = newel('button');
	bu.innerHTML = x.label;
	bu.addEventListener('pointerdown', function(e){
		update_playmode_chords(x.notes);
		let key = pianel.querySelector('#klawisz' + x.notes[0]);
		let event_params = { bubbles: true, cancelable: true, pointerType: 'mouse', isPrimary: true };
		let pointerDownEvent = new PointerEvent('pointerdown', event_params );
		key.dispatchEvent(pointerDownEvent);
	});
	let pointerup = function(e){
		let key = pianel.querySelector('#klawisz' + x.notes[0]);
		let event_params = { bubbles: true, cancelable: true, pointerType: 'mouse', isPrimary: true };
		let pointerUpEvent = new PointerEvent('pointerup', event_params );
		key.dispatchEvent(pointerUpEvent);
	};
	bu.addEventListener('pointerup', pointerup);
	bu.addEventListener('pointerleave', pointerup);
	bu.style.margin = '0.5em';
	return bu;
}

function rootName(root)
{
	let letter = ['C','C#/Db','D','D#/Eb','E','F','F#/Gb','G','G#/Ab','A','A#/Bb','B','B#/Cb'][root % 12];
	return letter.replace('b', '&flat;').replace('#', '&sharp;');
}
function chord_figures()
{
	function chord_svg(root, chord)
	{
		let params = {
			firstoctave: 3,
			lastoctave: 4,
			notes: 'C',
			marked: chord.map(x => x + root)
		};
		return svg_klawiatura(params);
	}
	function chord_figure(root, chord, chord_type)
	{
		let fig = newel('figure');
		let caption = newel('figcaption');
		caption.innerHTML = rootName(root) + ' ' + chord_type + ' chord';
		fig.append(caption, chord_svg(root, chord));
		return fig;
	}
	let roots = [60, 61, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59];
	let arr = [];
	for (let root of roots) arr.push(chord_figure(root, chord, g_chord_type));
	return arr;
}
function major_chord_figures()
{
	const root_position = [0, 4, 7];
	const first_inversion = [4, 7, 12];
	const second_inversion = [7, 12, 16];

	function chord_svg(root, chord)
	{
		let params = {
			firstoctave: 3,
			lastoctave: 5,
			notes: 'C',
			marked: chord.map(x => x + root)
		};
		return svg_klawiatura(params);
	}
	
	let roots = [48+12, 49+12, 50+12, 51+12, 52+12, 53+12, 54, 55, 56, 57, 58, 59];
	let arr = [];	
	for (let root of roots)
	{
		let fig = newel('figure');
		let caption = newel('figcaption');
		caption.innerHTML = rootName(root) + ' major chord in root position';
		fig.append(caption, chord_svg(root, root_position));
		arr.push(fig);
		
		fig = newel('figure');
		caption = newel('figcaption');
		caption.innerHTML = rootName(root) + ' major chord in first inversion';
		fig.append(caption, chord_svg(root, first_inversion));
		arr.push(fig);

		fig = newel('figure');
		caption = newel('figcaption');
		caption.innerHTML = rootName(root) + ' major chord in second inversion';
		fig.append(caption, chord_svg(root, second_inversion));
		arr.push(fig);		
	}
	return arr;
}
function minor_chord_figures()
{
	const root_position = [0, 3, 7];
	const first_inversion = [3, 7, 12];
	const second_inversion = [7, 12, 15];

	function chord_svg(root, chord)
	{
		let params = {
			firstoctave: 3,
			lastoctave: 5,
			notes: 'C',
			marked: chord.map(x => x + root)
		};
		return svg_klawiatura(params);
	}
	
	let roots = [48+12, 49+12, 50+12, 51+12, 52+12, 53+12, 54, 55, 56, 57, 58, 59];
	let arr = [];	
	for (let root of roots)
	{
		let fig = newel('figure');
		let caption = newel('figcaption');
		caption.innerHTML = rootName(root) + ' minor chord in root position';
		fig.append(caption, chord_svg(root, root_position));
		arr.push(fig);
		
		fig = newel('figure');
		caption = newel('figcaption');
		caption.innerHTML = rootName(root) + ' minor chord in first inversion';
		fig.append(caption, chord_svg(root, first_inversion));
		arr.push(fig);

		fig = newel('figure');
		caption = newel('figcaption');
		caption.innerHTML = rootName(root) + ' minor chord in second inversion';
		fig.append(caption, chord_svg(root, second_inversion));
		arr.push(fig);		
	}
	return arr;
}
function insert_chord_figures()
{
	let where = el('gallery');
	if (g_chord_type === 'major')
	{
		where.append(...major_chord_figures());
		return;
	}
	if (g_chord_type === 'minor')
	{
		where.append(...minor_chord_figures());
		return;
	}
	where.append(...chord_figures());
}
/*
function init_AdSense(clientId) {
  if (!clientId) throw new Error("init_AdSense: clientId is required");

  // Already initialized? bail.
  if (document.querySelector('script[src*="pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"]')) {
    return;
  }

  var s = document.createElement("script");
  s.async = true;
  s.src =
    "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=" +
    encodeURIComponent(clientId);
  s.crossOrigin = "anonymous";
  s.setAttribute("data-adsbygoogle", "loader");
  (document.head || document.documentElement).appendChild(s);
}
*/

function adsense_home_tall()
{
  let ins = newel("ins");
  ins.className = "adsbygoogle";
  ins.style.display = "block";
  ins.style.width = "100%";
  ins.setAttribute("data-ad-client", 'ca-pub-5700139411406455');
  ins.setAttribute("data-ad-slot", "2384034147");
  ins.setAttribute("data-ad-format", "auto");
  ins.setAttribute("data-full-width-responsive", "false");
  return ins;
}
function adsense_teclado_tall()
{
  let ins = newel("ins");
  ins.className = "adsbygoogle";
  ins.style.display = "block";
  ins.style.width = "100%";
  ins.setAttribute("data-ad-client", 'ca-pub-5700139411406455');
  ins.setAttribute("data-ad-slot", "1900834328");
  ins.setAttribute("data-ad-format", "auto");
  ins.setAttribute("data-full-width-responsive", "false");
  return ins;
}
function adsense_rupiano_tall()
{
  let ins = newel("ins");
  ins.className = "adsbygoogle";
  ins.style.display = "block";
  ins.style.width = "100%";
  ins.setAttribute("data-ad-client", 'ca-pub-5700139411406455');
  ins.setAttribute("data-ad-slot", "3938828397");
  ins.setAttribute("data-ad-format", "auto");
  ins.setAttribute("data-full-width-responsive", "false");
  return ins;
}
function adsense_insert_tall(lang)
{
	let col2 = el('column2');
	if (col2 === null) return;
	let w = parseFloat(getComputedStyle(col2).width) || 0;
	if (w === 0) return;
	if (col2.querySelector("ins.adsbygoogle")) return;
	
	if (lang === 'es')
	{
		col2.append(adsense_teclado_tall());
		console.log('inserted AdSense Teclado Tall');
	}
	else if (lang === 'ru')
	{
		col2.append(adsense_rupiano_tall());
		console.log('inserted AdSense Rupiano Tall');		
	}
	else
	{
		col2.append(adsense_home_tall());
		console.log('inserted AdSense Home Tall');
	}
	(window.adsbygoogle = window.adsbygoogle || []).push({});
}



function adsense_home728x90() // used for mobile too
{
  let ins = newel("ins");
  ins.className = "adsbygoogle";
  ins.style.display = "inline-block";
  ins.style.width = "728px";
  ins.style.height = '90px';
  ins.setAttribute("data-ad-client", 'ca-pub-5700139411406455');
  ins.setAttribute("data-ad-slot", "2165784592");
  return ins;
}
function adsense_teclado728x90() // used for mobile too
{
  let ins = newel("ins");
  ins.className = "adsbygoogle";
  ins.style.display = "inline-block";
  ins.style.width = "728px";
  ins.style.height = '90px';
  ins.setAttribute("data-ad-client", 'ca-pub-5700139411406455');
  ins.setAttribute("data-ad-slot", "8607904012");
  return ins;
}
function adsense_rupiano728x90() // used for mobile too
{
  let ins = newel("ins");
  ins.className = "adsbygoogle";
  ins.style.display = "inline-block";
  ins.style.width = "728px";
  ins.style.height = '90px';
  ins.setAttribute("data-ad-client", 'ca-pub-5700139411406455');
  ins.setAttribute("data-ad-slot", "2210016621");
  return ins;
}
function adsense_insert_wide(lang)
{
	let where = document.querySelector('aside.horizontal-ad');
	if (lang === 'es')
	{
		where.append(adsense_teclado728x90());
		console.log('inserted AdSense Teclado Wide');
	}
	else if (lang === 'ru')
	{
		where.append(adsense_rupiano728x90());
		console.log('inserted AdSense Rupiano Wide');
	}
	else
	{
		where.append(adsense_home728x90());
		console.log('inserted AdSense Home Wide');
	}
	(window.adsbygoogle = window.adsbygoogle || []).push({});
}

function pubmax_insert_desktop_wide(lang)
{
	let where = document.querySelector('aside.horizontal-ad');
	where.style.height = '280px';
	let desktop_home_wide =    "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_Desktop_underpiano'></div>";
	let desktop_teclado_wide = "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_telcado_Desktop_underpiano'></div>";
	let desktop_rupiano_wide = "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_pianino_Desktop_underpiano'></div>";
	if (lang === 'es')
	{
		where.innerHTML = desktop_teclado_wide;
		console.log('inserted PubMax Teclado Wide');
	}
	else if (lang === 'ru')
	{
		where.innerHTML = desktop_rupiano_wide;
		console.log('inserted PubMax Rupiano Wide');
	}
	else
	{
		where.innerHTML = desktop_home_wide;
		console.log('inserted PubMax Home Wide');
	}
}
function pubmax_insert_mobile(lang)
{
	let where = document.querySelector('aside.horizontal-ad');
	where.style.height = '280px';
	let mobile_home =    "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_Mobile_underpiano'></div>";
	let mobile_teclado = "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_telcado_Mobile_underpiano'></div>";
	let mobile_rupiano = "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_pianino_Mobile_underpiano'></div>";
	if (lang === 'es')
	{
		where.innerHTML = mobile_teclado;
		console.log('inserted PubMax Teclado Mobile');
	}
	else if (lang === 'ru')
	{
		where.innerHTML = mobile_rupiano;
		console.log('inserted PubMax Rupiano Mobile');
	}
	else
	{
		where.innerHTML = mobile_home;
		console.log('inserted PubMax Home Mobile');
	}
}
function pubmax_insert_tall(lang)
{
	let col2 = el('column2');
	if (col2 === null) return;
	let w = parseFloat(getComputedStyle(col2).width) || 0;
	if (w === 0) return;
	let home =    "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_Desktop_Right'></div>";
	let teclado = "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_telcado_Desktop_Right'></div>";
	let rupiano = "<div data-aaad='true' data-aa-adunit='/22247219933/VRTP_pianino_Desktop_Right'></div>";
	if (lang === 'es')
	{
		col2.innerHTML = teclado;
		console.log('inserted PubMax Teclado Tall');
	}
	else if (lang === 'ru')
	{
		col2.innerHTML = rupiano;
		console.log('inserted PubMax Rupiano Tall');
	}
	else
	{
		col2.innerHTML = home;
		console.log('inserted PubMax Home Tall');
	}
}

function insert_ads(lang)
{
	if (document.querySelector('script#adsense_init'))
	{
		adsense_insert_wide(lang);
		adsense_insert_tall(lang);
		return;
	}
	if (document.querySelector('script#pubmax_init'))
	{
		let ismobile = (window.innerWidth < 600);
		if (ismobile) pubmax_insert_mobile(lang); else pubmax_insert_desktop_wide(lang);
		pubmax_insert_tall(lang);
		return;
	}
}

function removeAds()
{
	let horad = document.querySelector('aside.horizontal-ad');
	if (horad) horad.remove();
	let tall = el('column2');
	if (tall)
	{
		tall.remove();
		el('twocolumns').style.display = 'block';
		el('twocolumns').style.minHeight = '0px';
	}
}


const g_chords_database = [{"identity": [0, 4, 7], "name": "major", "name_es": "mayor", "name_ru": "мажор", "nick": ["","M"], "display": ""},{"identity": [0, 3, 7], "name": "minor", "name_es": "menor", "name_ru": "минор", "nick": ["m","min"], "display": "m"},{"identity": [0, 3, 6], "name": "diminished triad voicing", "name_es": "disminuido", "name_ru": "уменьшённый", "nick": ["dim","°"], "display": "dim"},{"identity": [0, 4, 8], "name": "augmented", "name_es": "aumentado", "name_ru": "увеличенный", "nick": ["aug","+"], "display": "aug"},{"identity": [0, 4, 7, 10], "name": "dominant 7th", "name_es": "séptima dominante", "name_ru": "доминантсептаккорд", "nick": ["7"], "display": "<sup>7<\/sup>"},{"identity": [0, 4, 7, 11], "name": "major 7th", "name_es": "séptima mayor", "name_ru": "мажорный септаккорд", "nick": ["maj7","M7"], "display": "maj<sup>7<\/sup>"},{"identity": [0, 3, 7, 10], "name": "minor 7th", "name_es": "séptima menor", "name_ru": "минорный септаккорд", "nick": ["m7","min7"], "display": "m<sup>7<\/sup>"},{"identity": [0, 3, 6, 10], "name": "half-diminished 7th", "name_es": "séptima semidisminuida", "name_ru": "полууменьшённый септаккорд", "nick": ["m7b5","ø7"], "display": "m<sup>7♭5<\/sup>"},{"identity": [0, 3, 6, 9], "name": "diminished 7th", "name_es": "séptima disminuida", "name_ru": "уменьшённый септаккорд", "nick": ["dim7","°7"], "display": "dim<sup>7<\/sup>"},{"identity": [0, 2, 7], "name": "suspended 2nd", "name_es": "suspendida segunda", "name_ru": "суспендированный второй", "nick": ["sus2"], "display": "<sup>sus2<\/sup>"},{"identity": [0, 5, 7], "name": "suspended 4th", "name_es": "suspendida cuarta", "name_ru": "суспендированный четвёртый", "nick": ["sus4"], "display": "<sup>sus4<\/sup>"},{"identity": [0, 3, 7, 11], "name": "minor major 7th", "name_es": "menor séptima mayor", "name_ru": "минорный мажорный септаккорд", "nick": ["m(maj7)","mM7","mΔ7"], "display": "m<sup>M7<\/sup>"},{"identity": [0, 4, 8, 11], "name": "augmented major 7th", "name_es": "séptima mayor aumentada", "name_ru": "мажорный септаккорд с повышенной квинтой", "nick": ["maj7#5","+maj7"], "display": "aug<sup>M7<\/sup>"},{"identity": [0, 4, 8, 10], "name": "augmented 7th", "name_es": "séptima aumentada", "name_ru": "увеличенный септаккорд", "nick": ["7#5"], "display": "aug<sup>7<\/sup>"},{"identity": [0, 7], "name": "power chord", "name_es": "quinta (power chord)", "name_ru": "пауэр-аккорд", "nick": ["5"], "display": "5"}];

(() => {
  try {
    // Reuse a single namespace without clobbering if it already exists
    const NS = (window.PianoOrientation ||= {});

    // --- phone-only heuristic (excludes tablets/desktops) ---
    function isPhone() {
      try {
        if (navigator.userAgentData && typeof navigator.userAgentData.mobile === "boolean") {
          return navigator.userAgentData.mobile === true;
        }
        const ua = navigator.userAgent || "";
        const isIPad =
          /iPad/i.test(ua) ||
          (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1);
        const isAndroid = /Android/i.test(ua);
        const isMobileAndroid = /Android.*Mobile/i.test(ua);
        const isiPhone = /iPhone|iPod/i.test(ua);

        if (isIPad) return false;                    // iPadOS
        if (isAndroid && !isMobileAndroid) return false; // Android tablet

        const coarse = matchMedia("(pointer: coarse)").matches;
        const smallMinDim = Math.min(screen.width, screen.height) <= 600;

        return isiPhone || isMobileAndroid || (coarse && smallMinDim);
      } catch {
        // If anything fails, err on the safe side: not a phone
        return false;
      }
    }

    // --- snapshot current orientation ---
    function currentOrientation() {
      try {
        if (screen.orientation && screen.orientation.type) {
          return screen.orientation.type.startsWith("landscape") ? "landscape" : "portrait";
        }
        if (typeof window.orientation === "number") {
          return Math.abs(window.orientation) === 90 ? "landscape" : "portrait";
        }
        return matchMedia("(orientation: landscape)").matches ? "landscape" : "portrait";
      } catch {
        return "portrait";
      }
    }

    /**
     * Listen for explicit orientation changes (no resize),
     * and invoke `cb(details)` only on portrait -> landscape on smartphones.
     * Returns a cleanup function.
     */
    function onPortraitToLandscapePhone(cb) {
      let last = currentOrientation();
      let lastFireTs = 0;

      const useModern = !!(screen.orientation && "onchange" in screen.orientation);

      const handler = () => {
        try {
          const now = Date.now();
          if (now - lastFireTs < 150) return; // debounce burst

          const cur = currentOrientation();
          if (last !== "landscape" && cur === "landscape" && isPhone()) {
            lastFireTs = now;
            const payload = useModern
              ? {
                  source: "screen.orientation",
                  type: screen.orientation?.type || "unknown",
                  angle: screen.orientation?.angle ?? null,
                }
              : {
                  source: "window.orientation",
                  type: Math.abs(window.orientation) === 90 ? "landscape" : "portrait",
                  angle: window.orientation,
                };
            // Guard user callback
            try { cb(payload); } catch (err) { console.error("Orientation callback error:", err); }
          }
          last = cur;
        } catch (err) {
          console.error("Orientation handler error:", err);
        }
      };

      if (useModern) {
        screen.orientation.addEventListener("change", handler);
        return () => {
          try { screen.orientation.removeEventListener("change", handler); } catch {}
        };
      } else {
        window.addEventListener("orientationchange", handler);
        return () => {
          try { window.removeEventListener("orientationchange", handler); } catch {}
        };
      }
    }

    // Expose only the init; nothing else leaks into global scope
    NS.onPortraitToLandscapePhone = onPortraitToLandscapePhone;

    // Optional: no auto-run here. You call it when you want, e.g.:
    // const stop = PianoOrientation.onPortraitToLandscapePhone(({ source, type, angle }) => {
    //   // enter your piano focus mode here
    // });
    // later: stop();

  } catch (err) {
    // Any failure here won't affect your app
    console.error("PianoOrientation bootstrap failed:", err);
  }
})();
