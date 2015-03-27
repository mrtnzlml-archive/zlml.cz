Branches --- see http://nvie.com/posts/a-successful-git-branching-model/
========================================================================

MASTER	- verze aplikací, které jsou v souladu s ostrým nasazením

DEVELOP	- WIP větev

FEATURE	- dělá se z DEVELOP:
			git checkout -b myfeature develop
		- vrací se zpět do DEVELOP
			git checkout develop
			git merge --no-ff myfeature
			git branch -d myfeature
			git push origin develop

RELEASE	- konvence v názvu větví: release-* (release-1.2)
		- dělá se z DEVELOP
			git checkout -b release-1.2 develop
			// změna číslování verze
			git commit -a -m "Bumped version number to 1.2"
		- vrací se zpět do DEVELOP a do MASTER!
			git checkout master
			git merge --no-ff release-1.2
			git tag -a 1.2
			git checkout develop
			git merge --no-ff release-1.2
			git branch -d release-1.2

			git push --all //jen větve
			git push --tags //jen tagy
			git push --mirror //jako all+tags

HOTFIX	- konvence v názvu větví: hotfix-* (hotfix-1.2.1)
		- dělá se z MASTER
			git checkout -b hotfix-1.2.1 master
			// změna číslování verze
			git commit -a -m "Bumped version number to 1.2.1"
			git commit -m "Fixed severe production problem"
		- vrací se zpět do MASTER a DEVELOP!
			git checkout master
			git merge --no-ff hotfix-1.2.1
			git tag -a 1.2.1
			git checkout develop
			git merge --no-ff hotfix-1.2.1
			git branch -d hotfix-1.2.1