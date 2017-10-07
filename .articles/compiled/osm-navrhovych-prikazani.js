export default {
  "attributes": {
    "id": "6ef2212c-312f-45c5-a31b-bb9a7327ff42",
    "timestamp": 1356472874000,
    "title": "Osm návrhových přikázání",
    "slug": "osm-navrhovych-prikazani"
  },
  "body": "<p>Právě mám rozečtenou knihu, která popisuje návrhové vzory v PHP. Mimo jiné autor popisuje pravidla při návrhu softwaru, která jsou prokládána velkým množstvím ukázek a vysvětlivek. Celkem je na třiceti stranách knihy (kde je rozebírán návrh) schován následující seznam pravidel.</p>\n<ol>\n<li>Přístup k údajům vždy v rámci třídy zapouzdřete a poskytněte metody, pomocí nichž lze dané údaje získat.</li>\n<li>Svá rozhraní navrhujte tak, aby je bylo možné později rozšířit.</li>\n<li>V metodách tříd nezapouzdřujte jen údaje, ale také algoritmy, díky čemuž budou komplexní operace implementované centrálně na jednom místě.</li>\n<li>Znovupoužitelnost kódu je lepší než duplicitní kód.</li>\n<li>Vyvarujte se monolitickým strukturám a rozložte je na co nejmenší části, které mohou být implementované nezávisle na sobě. Pokud používáte rozsáhlé příkazy <code>if/elseif/else</code> nebo <code>switch</code>, popřemýšlejte, zda by se nedaly nahradit zaměnitelnými třídami.</li>\n<li>Dědění vede k neflexibilním strukturám. Na kombinaci různých funkcí používejte raději kompozice objektů.</li>\n<li>Vždy programujte vůči rozhraní, a nikdy ne vůči konkrétní implementaci.</li>\n<li>Vyhýbejte se těsným závislostem mezi jednotlivými třídami aplikace a vždy upřednostňujte volné vazby tříd.</li>\n</ol>\n<p>Další seznam který všichni znají, ale málokdo ho úplně dodržuje. (-:</p>\n"
}
