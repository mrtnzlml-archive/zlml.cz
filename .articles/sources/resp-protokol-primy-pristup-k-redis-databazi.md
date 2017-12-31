---
timestamp: 1419375234000
title: RESP protokol - přímý přístup k Redis databázi
slug: resp-protokol-primy-pristup-k-redis-databazi
---
[RESP](http://redis.io/topics/protocol) (**RE**dis **S**erialization **P**rotocol) je něco, s čím se asi většina lidí nepotká. Důvod je prostý. Tento protokol je většinou zabalen hluboko v knihovně, která pracuje s Redis databází. Existují však situace, kdy se tento protokol hodí. Jednou ze situací je stav, kdy potřebujete předat, nebo naopak získat z Redisu nějaká data a všechno ostatní vyjma RESP komunikace je zbytečné zdržování (u mě třeba sypání dat z procesoru přímo do Redisu). Druhý případ nastane v okamžiku, kdy potřebujete zajistit komunikaci klient-server a potřebujete zvolit vhodný formát přenosu informací. Jedná se tedy o použití tohoto protokolu úplně mimo Redis. Důvodů může být celá řada, nejhlavnější by však byl asi ten, že se s RESP formátem dat dá snadno pracovat, protože používá prefixový zápis.

# Formát RESP protokolu

RESP používá celkem 5 způsobů jak zaobalit přenášenou informaci. Vždy však platí, že první byte je byte určující o jaký formát se jedná:

- `+` jednoduchý string
- `-` error
- `:` integer
- `$` bulk string (binary safe)
- `*` array

Následuje samotný obsah, nebo dodatečné informace, například o délce a vše je ukončeno pomocí CRLF (`\r\n`). Postupně tedy přenášené informace moho vypadat například takto:

- `+PONG\r\n`
- `-Error 123\r\n`
- `:54986\r\n`
- `$4\r\nPING\r\n` (první část určuje délku bulk stringu, NULL je pak `$-1\r\n`)
- `*2\r\n$3\r\nGET\r\n$3\r\nkey\r\n` (první je délka pole, následuje kombinace předchozích)

To je celé, žádná věda v tom není. Je to skutečně jednoduchý protokol a to je super, protože se s ním dá snadno pracovat. Navíc lze poslat celý dlouhý text obsahující více příkazů v jednom spojení.

# Implementace v Node.js

V Node.js by teď měla být realizace velmi jednoduchá. Napíšeme si krátký TCP client, který se nám napojí na Redis databázi (většinou port 6379) a budeme posílat/přijímat data:

```javascript
var net = require('net');

var client = net.connect({port: 6379}, function() {
	console.log('Connected to the Redis server.');

	client.write('*1\r\n$4\r\nPING\r\n');
	client.write('*3\r\n$3\r\nSET\r\n$3\r\nkey\r\n$5\r\nxxxxx\r\n');
	client.write('*2\r\n$3\r\nGET\r\n$3\r\nkey\r\n');
	client.write('*5\r\n$5\r\nPFADD\r\n$11\r\nHyperLogLog\r\n$3\r\nxxx\r\n$3\r\nyyy\r\n$3\r\nzzz\r\n');
	client.write('*2\r\n$7\r\nPFCOUNT\r\n$11\r\nHyperLogLog\r\n');

	client.write('*1\r\n$7\r\nFLUSHDB\r\n');
});

client.on('data', function(data) {
	console.log(data.toString());
	client.end();
});

client.on('end', function() {
	console.log('Disconnected from the Redis server.');
});
```

Proč jsou udesílaná data zabalena v RESP poli? Vychází to z toho, že podle dokumentace, by klient měl posílat na server pole bulk stringů. Nicméně dobře funkční a validní zápis je i bez pole (`client.write('GET key\r\n');`) jen musí být opět ukončen pomocí CRLF.

Odesíláme do Redis databáze celkem 6 příkazů. První je obyčejný `PING`, následuje `SET` a `GET` klíče, `PFADD` a `PFCOUNT` z HyperLogLog datového dypu a nakonec jen smazání databáze. Co bude výstupem?

```
+PONG
+OK
$5
xxxxx
:0
:3
+OK
```

Jak je možné, že server vrátil 7 odpovědí? Je to prosté, upravíme si datový callback ať je zřejmé, co skutečně dostáváme za data:

```javascript
client.on('data', function(data) {
	console.log(JSON.stringify(data.toString()));
	client.end();
});
```

Teď už bude výstup o něco jiný:

```
"+PONG\r\n+OK\r\n$5\r\nxxxxx\r\n:0\r\n:3\r\n+OK\r\n"
```

A vše již dává smysl. První odpověď je `PONG` (na `PING`), následuje reakce `OK` na nastavení klíče, odpověď ve formě bulk stringu, který má dvě části - délku a samotnou textovou odpověď (proto to odřádkování navíc), následuje odpověď z `PFADD` (0 nebo 1 podle situace) a také odpověď z `PFCOUNT` (mohutnost množiny v HyperLogLog). Poslední `OK` je reakce na `FLUSHDB`. Jak je vidět, tak i v odpovědi je prvním znakem formát dat dané odpovědi.

Takto jsem to celkem zbytečně (ale pro přehlednost) rozepisoval. Celá komunikace směrem k serveru by se dala napsat do jednoho požadavku:

```
*1\r\n$4\r\nPING\r\n*3\r\n$3\r\nSET\r\n$3\r\nkey\r\n$5\r\nxxxxx\r\n*2\r\n$3\r\nGET\r\n$3\r\nkey\r\n*5\r\n$5\r\nPFADD\r\n$11\r\nHyperLogLog\r\n$3\r\nxxx\r\n$3\r\nyyy\r\n$3\r\nzzz\r\n*2\r\n$7\r\nPFCOUNT\r\n$11\r\nHyperLogLog\r\n*1\r\n$7\r\nFLUSHDB\r\n
```

Odpověď by zůstala stejná.