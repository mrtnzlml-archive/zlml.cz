export default {
  "attributes": {
    "id": "6ef2212c-312f-45c5-a31b-bb9a7327ff42",
    "timestamp": 1356472874000,
    "title": "Osm návrhových přikázání",
    "slug": "osm-navrhovych-prikazani"
  },
  "body": "Právě mám rozečtenou knihu, která popisuje návrhové vzory v PHP. Mimo jiné autor popisuje pravidla při návrhu softwaru, která jsou prokládána velkým množstvím ukázek a vysvětlivek. Celkem je na třiceti stranách knihy (kde je rozebírán návrh) schován následující seznam pravidel.\n\n1. Přístup k údajům vždy v rámci třídy zapouzdřete a poskytněte metody, pomocí nichž lze dané údaje získat.\n2. Svá rozhraní navrhujte tak, aby je bylo možné později rozšířit.\n3. V metodách tříd nezapouzdřujte jen údaje, ale také algoritmy, díky čemuž budou komplexní operace implementované centrálně na jednom místě.\n4. Znovupoužitelnost kódu je lepší než duplicitní kód.\n5. Vyvarujte se monolitickým strukturám a rozložte je na co nejmenší části, které mohou být implementované nezávisle na sobě. Pokud používáte rozsáhlé příkazy <code>if/elseif/else</code> nebo <code>switch</code>, popřemýšlejte, zda by se nedaly nahradit zaměnitelnými třídami.\n6. Dědění vede k neflexibilním strukturám. Na kombinaci různých funkcí používejte raději kompozice objektů.\n7. Vždy programujte vůči rozhraní, a nikdy ne vůči konkrétní implementaci.\n8. Vyhýbejte se těsným závislostem mezi jednotlivými třídami aplikace a vždy upřednostňujte volné vazby tříd.\n\nDalší seznam který všichni znají, ale málokdo ho úplně dodržuje. (-:"
}
