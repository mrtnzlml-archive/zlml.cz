Čím více mi proteklo kódu pod prsty, tím více mě testování baví. Nebaví mě psát super skvělé testy, ale baví mě brát testy jako součást vývoje. Mnohdy je to dokonce nejpohodlnější způsob. Na následujících řádcích ukážu jak se dá napsat algoritmus pro [korelaci velkého množství dat](https://en.wikipedia.org/wiki/Correlation_and_dependence) a hlavně **jak jej otestovat**. Konkrétně půjde o Pearsonův korelační koeficient.

Pearsonův korelační koeficient lze popsat velmi jednoduchým vzorcem (podíl kovariance a směrodatných odchylek souborů dat - viz wiki). Implementace tohoto vzorce v MATLABu může vypadat třeba takto:

```
function rho = pearson(obj, X, Y)
    X = X(:);
    Y = Y(:);
    covariance = cov(X, Y);
    rho = covariance(1, 2) / (std(X) * std(Y));
    if isnan(rho)
        error('pearson:undefined', 'Correlation coefficient is undefined because the variance of Y is zero.')
    end
end
```

Tento vzorec vrací číslo v intervalu od -1 do +1, které charakterizuje míru závislosti Y na X. Jak toto chování otestovat? V MATLABu existuje připravený [TestCase](https://www.mathworks.com/help/matlab/ref/matlab.unittest.testcase-class.html) (od verze R2013a), takže pokud znáte TestCase třeba z Nette\Testeru, tak je to velmi jednoduché (čti skoro stejné). Připravíme si samotnou testovací třídu:

```
classdef CorrelationTest < matlab.unittest.TestCase
    methods (Test)
        % TODO - test methods
    end
end
```

A zbývá pouze doplnit pár základních testů. Zde bude vidět, že se vyplatí mít korelace implementované v rámci nějaké třídy. První test by mohl kontrolovat perfektní lineární závislost:

```
function testPearsonCorrelated(testCase)
    % actual, expected, ...
    X1 = [1 2 3 4]; Y1 = [1 2 3 4];
    X2 = [1 2 3 4]; Y2 = [1 3 5 7];
    X3 = [1 3 5 7]; Y3 = [1 2 3 4];
    testCase.verifyEqual(pearson(Correlation, X1, Y1), 1.0, 'AbsTol', 1e-15);
    testCase.verifyEqual(pearson(Correlation, X2, Y2), 1.0, 'AbsTol', 1e-15);
    testCase.verifyEqual(pearson(Correlation, X3, Y3), 1.0, 'AbsTol', 1e-15);
end
```

Testo test otestuje tři různě strmé lineární závislosti. Pearson by neměl na strmost reagovat - vždy by tento test měl vyjít +1. Pokud by se někomu nelíbilo opakování verify metod, lze přepsat tento test trošku chytřeji:

```
function testPearsonCorrelated(testCase)
    assert = @(x, y) testCase.verifyEqual(pearson(Correlation, x, y), 1.0, 'AbsTol', 1e-15);
    assert([1 2 3 4], [1 2 3 4]);
    assert([1 2 3 4], [1 3 5 7]);
    assert([1 3 5 7], [1 2 3 4]);
end
```

Výsledek je stejný jen s pomocí [anonymní funkce](https://www.mathworks.com/help/matlab/matlab_prog/anonymous-functions.html). Důležitá je v tomto testu pouze metoda `verifyEqual`, která dělá přesně to co říká s tím, že se v tomto testu ignoruje zaokrouhlovací chybu (velmi malá absolutní tolerance).

Tímto způsobem lze jednoduše Pearson metodu pořádně otestovat. Celá implementace i s testy je [k dispozici online](https://gist.github.com/mrtnzlml/d12a93d0ce5bced2900dfbaee003c796) (včetně Spearmanova algoritmu). Poslední test který ukážu je [Anscombe's quartet](https://en.wikipedia.org/wiki/Anscombe%27s_quartet) test. Zde je k dispozici sada hodnot, která vždy vytvoří jinou grafickou reprezentaci dat, ale Pearsonův korelační koeficient je pořád stejný. Opět mohu s výhodou využít anonymní funkci:

```
function testAnscombesQuartet(testCase)
    c = Correlation(false);
    test = @(x, y) testCase.verifyEqual(pearson(c, x, y), 0.816, 'RelTol', 0.001);
    X1 = [10 8 13 9 11 14 6 4 12 7 5];
    Y1 = [8.04 6.95 7.58 8.81 8.33 9.96 7.24 4.26 10.84 4.82 5.68];
    test(X1, Y1);
    X2 = [10 8 13 9 11 14 6 4 12 7 5];
    Y2 = [9.14 8.14 8.74 8.77 9.26 8.10 6.13 3.10 9.13 7.26 4.74];
    test(X2, Y2);
    X3 = [10 8 13 9 11 14 6 4 12 7 5];
    Y3 = [7.46 6.77 12.74 7.11 7.81 8.84 6.08 5.39 8.15 6.42 5.73];
    test(X3, Y3);
    X4 = [8 8 8 8 8 8 8 19 8 8 8];
    Y4 = [6.58 5.76 7.71 8.84 8.47 7.04 5.25 12.50 5.56 7.91 6.89];
    test(X4, Y4);
end
```

Každá sada bodů vyjde vždy 0.816 s relativní tolerancí 0.001. A konečně spuštění této sady testů:

```
>> run(CorrelationTest)
Running CorrelationTest
.........
Done CorrelationTest
__________


ans = 

  1×9 TestResult array with properties:

    Name
    Passed
    Failed
    Incomplete
    Duration
    Details

Totals:
   9 Passed, 0 Failed, 0 Incomplete.
   0.14742 seconds testing time.

>>
```

Když se teď člověk podívá zpět, tak je vidět, že psát a následně upravovat a rozšířovat takovou věc je bez testů šílenost. Přesto psaní testů v MATLABu není žádnou zvyklostí. A jak jinak při vývoji ověřovat, že vše stále funguje? Však už si dávno nepamatujete kolik má vyjít Pearsonův korelační koeficient u Anscombe's quartetu... :-)