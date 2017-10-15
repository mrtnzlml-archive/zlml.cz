---
timestamp: 1413669750000
title: Nastavení TIM3 na STM32F207xx
slug: nastaveni-tim3-na-stm32f207xx
---
Tento článek už mám dlouhou dobu v hlavě, ale nikdy jsem se neodvážil jej sepsat. Má to svůj smysl. Jedná se o poměrně náročnou problematiku, kterou si myslím, že moc lidí nechápe. Rád bych tedy tímto popíchnul čtenáře o zpětnou vazbu a pokud by se ukázalo, že to smysl má, pokračoval bych někam dál do složitějších věcí, protože toto bude ve výsledku směšně jednoduché... (-:

![](https://zlmlcz-media.s3-eu-west-1.amazonaws.com/f34b6d82-9aaf-4089-afe6-81993d596885/stm32.png)

K dispozici mám momentálně dva procesory, ale budu řešit konkrétně `STM32F207xx`. Ten druhý je ze `4xx` řady, ale díky novým Cube knihovnám není v programech žádný rozdíl. Alespoň ne v tom, co bu ukazovat dnes. [STM32F207](http://www.st.com/web/catalog/mmc/FM141/SC1169/SS1575/LN9/PF245085) je procesor založený na 32-bit ARM(R) Cortex(R)-M3 od STMicroelecronics. Jedná se o procesor s 120 MHz jádrem, 1024 kB flash pamětí na program a 128 kB SRAM. Celkově se jedná o poměrně výkonnou mršku na které se toho nechá upočítat poměrně hodně a hlavně dokáže ovládat velké množství rozmanitých periferií viz obrázek. Na obrázku je přesně ta samá eval deska s tím samým procesorem, na kterou právě teď koukám. Nejzajímavější bude teď však právě onen procesor, který je uprostřed desky a SMD diody, které nejsou téměř vůbec vidět. Ty jsou umístěny nad Wakeup tlačítkem téměř pod displejem.

A teď konečně o čem budu psát. TIM3 je timer ze skupiny TIM2-5. Jedná se o timer naprosto běžný a obyčejný. Celkem jich je k dispozici 14 ve 4 skupinách podle společných vlastností. Podívat se na ně můžete do [referenční příručky](http://www.st.com/web/en/resource/technical/document/reference_manual/DM00031020.pdf), ale pozor ať se vám z toho nezamotá hlava, je toho tam fak hodně... (-: Ukážeme jak tento timer nastavit tak aby bylo možné blikat diodou jednou za 1000 ms. Navíc k tomu nebudeme využívat výkonu procesoru, ale využijeme přerušení, takže nebudeme procesor vůbec ničím zdržovat. O zrovna čekání procesoru 1000 ms, než bude moci bliknout diodou by byl slušný zabiják výkonu.

# Nastavení TIMx a diod

Je to jednudché. V souboru `main.c` existuje klasická viod metoda s while smyčkou, která bude na začátku i na konci tohoto programu prázdná:

```cpp
#include "main.h"
int main(void) {
	HAL_Init();
    SystemClock_Config();
	# ...
	while (1) {
	}
}
```

Cílem je neudělat žádnou blbost a nechat procesor nejlépe bez toho aby něco musel počítat, což je díky přerušení možné a doporučované. HAL (Hardware abstraction layer) vrstu a systémové hodiny nastavíme co nejdříve. `HAL_Init();` je systémová funkce Cube knihoven, která provede inicializaci HW abstraktní vrstvy. Ve skutečnosti HAL_Init spouští celou řadu dalších initů. `SystemClock_Config();` je již uživatelská metoda, která je umístěna v každém demu v Cube Examples, takže ji sem nebudu přepisovat. Je téměř vždy stejná. Následovat by však měla inicializace diod. Dalo by se to udělat i složitě, ale s využitím BSP (Board support package) je inicializace triviální:

```cpp
BSP_LED_Init(LED1);
# LED2, LED3, LED4
```

Teď tedy máme nastartovanou desku, hodiny a připravené diody k použití. Nezbývá než se pustit do nastavování timeru:

```cpp
/*##-1- Configure the TIM peripheral #######################################*/
TimHandle.Instance = TIMx;
TimHandle.Init.Period = 10000;
TimHandle.Init.Prescaler = (uint32_t)(((SystemCoreClock / 2) / 10000) - 1); //10kHz
// T = 1/f = 1/10k = 0,0001 ; time = Period * T = 1s
TimHandle.Init.ClockDivision = 0;
TimHandle.Init.CounterMode = TIM_COUNTERMODE_UP;
if(HAL_TIM_OC_Init(&TimHandle) != HAL_OK) {
	Error_Handler();
}
```

`TIMx` je v `main.h` nastaven na TIM3, period je délka intervalu a prescaler je předdělička frekvence, která je díky tomu výpočtu nastavena nezávisle na rychlosti hodin na jednotnou frekvenci 10 kHz. Teď si stačí jen oprášit středoškolskou elektroniku. Pokud víme, že `T = 1/f` a frekvence je 10 000 Hz, pak je perioda takového průběhu 0,1 ms. To je docela průser, protože je to srašně rychlé. Proto je zde právě ta perioda timeru, kde v podstatě říkáme, že toto má proběhnout 10 000x a pak až cvrnknout timerem. A voilà, máme 1 vteřinu. `TIM_COUNTERMODE_UP` přávě říká, že bude počítat dokud nenarazí na strop a pak timer přeteče. Snad jedině pozor na jednu zradu. TIM3 má prescaler 16 bitový, tzn. lze nastavit maximálně hodnotu 2^16 -1 = 65535! Vzhledem k tomu, že to z výpočtu není přímo vidět, tak se na to můžete snadno nachytat a timer pak poběží jinak než bylo požadováno. Dále si nastavíme kanál, který budeme využívat k blikání diodou, protože teď timer pouze dojede na 10 000 a vyresetuje se:

```cpp
/*##-2- Configure the Output Compare channels #########################################*/
sConfig.OCMode = TIM_OCMODE_TOGGLE;
sConfig.Pulse = uhCCR1_Val;
sConfig.OCPolarity = TIM_OCPOLARITY_LOW;
if(HAL_TIM_OC_ConfigChannel(&TimHandle, &sConfig, TIM_CHANNEL_1) != HAL_OK) {
	Error_Handler();
}
sConfig.Pulse = uhCCR2_Val;
if(HAL_TIM_OC_ConfigChannel(&TimHandle, &sConfig, TIM_CHANNEL_2) != HAL_OK) {
	Error_Handler();
}
```

Před vstupem do main metody je zapotřebí nastavit si ještě pulse hodnoty:

```cpp
__IO uint32_t uhCCR1_Val = 100;
__IO uint32_t uhCCR2_Val = 200;
```

Abych to krátce vysvětlil. Tímto jsem si nastavil, že až timer dosáhne hodnoty 100 a 200, tak cvrnkne a to pokaždé do jiného kanálu. Při dosažení maxima (10 000) dojde k přetečení timeru a ten začne počítat znovu. Je jedno jak tyto hodnoty nastavím, ale musí být od sebe 100 ms. Chci totiž zapnout diodu a dned ji vypnout. Toto se bude opakovat každných 1000 ms viz předchozí nastavení timeru. Super, takže teď máme timer co počítá a v určitém okamžiku pošle dva signály. Tak to nahodíme a jedeme... (-:

```cpp
/*##-3- Start signals generation #######################################*/
/* Start channel 1 in Output compare mode */
if(HAL_TIM_OC_Start_IT(&TimHandle, TIM_CHANNEL_1) != HAL_OK) {
	Error_Handler();
}
if(HAL_TIM_OC_Start_IT(&TimHandle, TIM_CHANNEL_2) != HAL_OK) {
	Error_Handler();
}
```

Skvělé na tom je to, že teď už to fakt cvaká a stačí se na to jen pověsit přepínání stavu diod.

# Konečně blikáme!

K tomu, aby bylo možné blikat, musíme se chytit callbacku, který je v HAL připraven. Zde se podíváme, jestli je daný kanál aktivní a pokud ano, znamená to, že můžeme něco udělat. V tomto případě tedy nejdříve zapnu diodu a za 100 ms přijde signál druhým kanálem a já ji mohu opět vypnout. Za 1s se celý proces opakuje. Paráda!

```cpp
/**
  * @brief  Output Compare callback in non blocking mode 
  * @param  htim : TIM OC handle
  * @retval None
  */
void HAL_TIM_OC_DelayElapsedCallback(TIM_HandleTypeDef *htim) {
	if(htim->Channel == HAL_TIM_ACTIVE_CHANNEL_1) {
		BSP_LED_On(LED1);
  	}
	if(htim->Channel == HAL_TIM_ACTIVE_CHANNEL_2) {
		BSP_LED_Off(LED1);
  	}
}
```

Ještě bych měl zmínit metodu `Error_Handler();`, která se zde často opakuje. Je to metoda, která se zavolá, když se něco nepovede a její obsah může být opět prázdná smyčka, aby procesor nezačal dělat nějaké nesmysly:

```cpp
/**
  * @brief  This function is executed in case of error occurrence.
  * @param  None
  * @retval None
  */
static void Error_Handler(void) {
	BSP_LED_On(LED4);
	while(1) {}
}
```

Asi není úplně hloupé zapnout i nějakou (červenou) diodu, která bude signalizovat error. Rád bych zmínil to, že by bylo možné v main metodě v cyklu spustit `BSP_LED_Toggle(LED4);` a počkat 1 vteřinu. To by bylo funkční, ale jak by se ukázalo časem, tak by tento program mohl sloužit pouze pro blikání diodou, což není moc užitečné. Pokud bych se k tomu dostal, tak si můžeme ukázat jak pracovat s ethernetem a zde se ukáže, že je prázdný while potřeba. Tímto způsobem mi však nic nebrání blikat si diodou a vedle toho ještě tlačit data ethernetem pryč...

Tak co, dalo se to vydržet? :-)