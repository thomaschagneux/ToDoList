# Guide de Migration de Symfony 3.1 vers Symfony 7.x

## Situation Actuelle

Votre projet est dans une situation particulière :
- Structure de code et organisation : Symfony 3.1 (ancienne structure)
- Dépendances installées : Symfony 7.2 (version moderne)

Cette différence explique pourquoi la structure du projet vous semble différente des projets Symfony auxquels vous êtes habitué.

## Problèmes Potentiels

Cette configuration mixte peut causer plusieurs problèmes :
1. Incompatibilités entre le code écrit pour Symfony 3.1 et les bibliothèques Symfony 7.2
2. Fonctionnalités dépréciées ou supprimées dans les versions récentes
3. Différences dans la structure des répertoires et la configuration
4. Différences dans la façon dont les services sont injectés et utilisés

## Options pour Résoudre le Problème

### Option 1 : Revenir à Symfony 3.1 (Solution temporaire)

Si vous avez besoin de travailler rapidement avec le projet sans le migrer :

1. Modifiez votre `composer.json` pour revenir à Symfony 3.1 :
```
"require": {
    "php": ">=5.5.9",
    "symfony/symfony": "3.1.*",
    "doctrine/orm": "^2.5",
    "doctrine/doctrine-bundle": "^1.6",
    "doctrine/doctrine-cache-bundle": "^1.2",
    "symfony/swiftmailer-bundle": "^2.3",
    "symfony/monolog-bundle": "^2.8",
    "symfony/polyfill-apcu": "^1.0",
    "sensio/distribution-bundle": "^5.0",
    "sensio/framework-extra-bundle": "^3.0.2",
    "incenteev/composer-parameter-handler": "^2.0"
},
```

2. Exécutez `composer update`

### Option 2 : Migration Progressive vers Symfony 7.x (Recommandée)

Pour une solution à long terme, migrez progressivement le projet :

1. **Créez un nouveau projet Symfony 7.x** :
   ```
   composer create-project symfony/skeleton:"7.2.*" new-project
   ```

2. **Migrez les fonctionnalités une par une** :
   - Commencez par les entités et les repositories
   - Puis les services
   - Ensuite les contrôleurs
   - Enfin les templates et les assets

3. **Adaptez le code aux nouvelles pratiques** :
   - Utilisez les attributs PHP au lieu des annotations
   - Utilisez l'injection de dépendances au lieu de `$this->get()`
   - Utilisez `AbstractController` au lieu de `Controller`
   - Adaptez les routes et la configuration de sécurité

### Option 3 : Migration Directe (Plus Complexe)

Si vous préférez garder le projet actuel et le migrer directement :

1. **Créez une branche pour la migration**

2. **Mettez à jour la structure des répertoires** :
   - Déplacez les fichiers de `web/` vers `public/`
   - Déplacez la configuration de `app/config/` vers `config/`
   - Créez les répertoires manquants selon la structure Symfony 7.x

3. **Mettez à jour les fichiers de configuration** :
   - Convertissez les fichiers YAML en PHP si nécessaire
   - Adaptez la configuration aux nouveaux formats

4. **Mettez à jour le code** :
   - Remplacez les annotations par des attributs
   - Mettez à jour les contrôleurs et les services
   - Adaptez les templates Twig si nécessaire

## Différences Principales entre Symfony 3.1 et Symfony 7.x

### Structure des Répertoires

**Symfony 3.1** :
```
app/
├── config/
├── Resources/
└── AppKernel.php
src/
└── AppBundle/
web/
```

**Symfony 7.x** :
```
config/
├── packages/
├── routes/
└── services.yaml
public/
src/
└── Controller/
└── Entity/
```

### Configuration

**Symfony 3.1** : Principalement YAML dans `app/config/`
**Symfony 7.x** : PHP ou YAML dans `config/`

### Contrôleurs

**Symfony 3.1** :
```php
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        // ...
    }
}
```

**Symfony 7.x** :
```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(Request $request, SecurityAuthenticationUtils $authenticationUtils): Response
    {
        // Injection de dépendances directe
        // ...
    }
}
```

## Ressources Utiles

- [Documentation officielle de Symfony](https://symfony.com/doc/current/index.html)
- [Guide de mise à niveau de Symfony](https://symfony.com/doc/current/setup/upgrade_major.html)
- [Symfony Flex](https://symfony.com/doc/current/setup/flex.html)
- [Bonnes pratiques Symfony](https://symfony.com/doc/current/best_practices.html)

## Conclusion

La migration d'un projet Symfony 3.1 vers Symfony 7.x est un processus significatif qui nécessite une planification et une exécution soigneuses. Selon vos contraintes de temps et vos objectifs, choisissez l'option qui convient le mieux à votre situation.

Pour un projet d'apprentissage comme celui-ci (basé sur OpenClassrooms), l'option 2 (migration progressive) est généralement la plus éducative et la plus sûre.
