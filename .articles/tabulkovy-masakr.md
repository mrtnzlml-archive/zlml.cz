---
id: 6dcf0031-975d-4eb2-85d6-e4e00d02dd4a
timestamp: 1349557092000
title: Tabulkový masakr
slug: tabulkovy-masakr
---
Určitě znáte HTML a tím pádem znáte i tabulky. Pro jistotu připomenutí.
Tabulka se v HTML tvoří párovým tagem <code>&lt;table&gt;&lt;/table&gt;</code>, její řádky jsou <code>&lt;tr&gt;&lt;/tr&gt;</code> a buňky <code>&lt;td&gt;&lt;/td&gt;</code>. Buňky mají volitelné atributy <code>rowspan</code> a <code>colspan</code>. Tyto atributy určují, kolik bude buňka zabírat místa v řádce, resp. ve sloupci. Tyto znalosti stačí k pochopení pojmu "tabulkový paradox".

Podívejte se na první ukázku:

<table border="1" cellpadding="6" cellspacing="2" width="400">
	<tr>
		<td width="33%">A1</td>
		<td width="33%" rowspan="4">A2</td>
		<td width="33%">A3</td>
	</tr>
	<tr>
		<td colspan="3">B1</td>
	</tr>
	<tr>
		<td>C1</td>
		<td>C3</td>
	</tr>
	<tr>
		<td>D1</td>
		<td>D3</td>
	</tr>
</table>

Zde je vidět co se stane, když roztáhneme buňku **A2** na tři řádky a zároveň roztáhneme buňku **B1** na tři sloupce podle následujícího kódu:
```html
<table border="1" cellpadding="6" cellspacing="2" width="400">
	<tr>
		<td width="33%">A1</td>
		<td width="33%" rowspan="4">A2</td>
		<td width="33%">A3</td>
	</tr>
	<tr>
		<td colspan="3">B1</td>
	</tr>
	<tr>
		<td>C1</td>
		<td>C3</td>
	</tr>
	<tr>
		<td>D1</td>
		<td>D3</td>
	</tr>
</table>
```
Tam kde by měla být buňka **B2** vzniká krásné okénko do Narnie, které je společné jako pro druhý sloupec, tak pro druhý řádek.

Důsledkem neopatrného zacházení se spojováním sloupců může být výsledek viditelný ve druhé ukázce:

<table border="1" cellpadding="6" cellspacing="2" width="400">
	<tr>
		<td width="33%" colspan="4">A1</td>
		<td width="33%" rowspan="4">A2</td>
		<td width="33%" colspan="4">A3</td>
		<td width="33%" rowspan="4">A4</td>
	</tr>
	<tr>
		<td width="33%" colspan="4">B1</td>
		<td width="33%" rowspan="4">B2</td>
		<td width="33%" colspan="4">B3</td>
		<td width="33%" rowspan="4">B4</td>
	</tr>
	<tr>
		<td width="33%" colspan="4">C1</td>
		<td width="33%" rowspan="4">C2</td>
		<td width="33%" colspan="4">C3</td>
		<td width="33%" rowspan="4">C4</td>
	</tr>
	<tr>
		<td width="33%" colspan="4">D1</td>
		<td width="33%" rowspan="4">D2</td>
		<td width="33%" colspan="4">D3</td>
		<td width="33%" rowspan="4">D4</td>
	</tr>
</table>

<br />

<table border="1" cellpadding="6" cellspacing="2" width="400">
	<tr>
		<td width="33%" rowspan="4">A1</td>
		<td width="33%" colspan="4">A2</td>
		<td width="33%" rowspan="4">A3</td>
		<td width="33%" colspan="4">A4</td>
	</tr>
	<tr>
		<td width="33%" rowspan="4">B1</td>
		<td width="33%" colspan="4">B2</td>
		<td width="33%" rowspan="4">B3</td>
		<td width="33%" colspan="4">B4</td>
	</tr>
	<tr>
		<td width="33%" rowspan="4">C1</td>
		<td width="33%" colspan="4">C2</td>
		<td width="33%" rowspan="4">C3</td>
		<td width="33%" colspan="4">C4</td>
	</tr>
	<tr>
		<td width="33%" rowspan="4">D1</td>
		<td width="33%" colspan="4">D2</td>
		<td width="33%" rowspan="4">D3</td>
		<td width="33%" colspan="4">D4</td>
	</tr>
</table>

<br />

<table border="1" cellpadding="6" cellspacing="2" width="400">
	<tr>
		<td width="33%" colspan="4">A1</td>
		<td width="33%" rowspan="4">A2</td>
		<td width="33%" colspan="4">A3</td>
		<td width="33%" colspan="4">A4</td>
	</tr>
	<tr>
		<td width="33%" rowspan="4">B1</td>
		<td width="33%" colspan="4">B2</td>
		<td width="33%" rowspan="4">B3</td>
		<td width="33%" rowspan="4">B4</td>
	</tr>
	<tr>
		<td width="33%" colspan="4">C1</td>
		<td width="33%" rowspan="4">C2</td>
		<td width="33%" colspan="4">C3</td>
		<td width="33%" colspan="4">C4</td>
	</tr>
	<tr>
		<td width="33%" colspan="4">D1</td>
		<td width="33%" rowspan="4">D2</td>
		<td width="33%" colspan="4">D3</td>
		<td width="33%" colspan="4">D4</td>
	</tr>
</table>

Za domácí úkol se pokuste napsat alespoň jednu podobnou tabulku bez nahlédnutí do zdrojového kódu... :-)

A poslední perlička vyvracející tvrzení, že párový element <code>&lt;tr&gt;&lt;/tr&gt;</code> vytváří v tabulce novou řádku. Podívejte se na následující kód. Hádám, že už je vám jasné co se stane.
```html
<table border="1" cellpadding="6" cellspacing="2" width="400">
	<tr><td width="33%" rowspan="4">Řádka_1</td></tr>
	<tr><td width="33%" rowspan="3">Řádka_1</td></tr>
	<tr><td width="33%" rowspan="2">Řádka_1</td></tr>
	<tr><td width="33%" rowspan="1">Řádka_1</td></tr>
</table>
```
Výsledek je vidět ve třetí ukázce:

<table border="1" cellpadding="6" cellspacing="2" width="400">
	<tr><td width="33%" rowspan="4">Řádka_1</td></tr>
	<tr><td width="33%" rowspan="3">Řádka_1</td></tr>
	<tr><td width="33%" rowspan="2">Řádka_1</td></tr>
	<tr><td width="33%" rowspan="1">Řádka_1</td></tr>
</table>

Vyzkoušejte, že se tabulkový paradox vykresluje ve všech prohlížečích stejně. Mám to tedy chápat tak, že tolik oblíbený Chrome je stejný shit jako IE? Kdepak... Jedná se opravdu o paradox, který by sice měl (prapodivné) řešení((výsledek potlačující paradox, vyvolávající další spory)), ale pak by byl porušen význam některých atributů buňek tabulky.