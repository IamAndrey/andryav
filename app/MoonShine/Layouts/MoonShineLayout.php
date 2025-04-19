<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Components\UserProfile;
use App\MoonShine\Resources\Comment\CommentResource;
use App\MoonShine\Resources\Project\DraftProjectResource;
use App\MoonShine\Resources\Project\ModerationProjectResource;
use MoonShine\MenuManager\MenuGroup;
use App\MoonShine\Resources\User\UserResource;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Components\Layout\{Locales, Notifications, Search};
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\{Breadcrumbs,
    Components,
    Layout\Flash,
    Layout\Div,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Footer,
    Layout\Head,
    Layout\Favicon,
    Layout\Assets,
    Layout\Meta,
    Layout\Header,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\TopBar,
    Layout\Wrapper,
    When};
use Domain\Project\Models\Project;
use App\MoonShine\Resources\Project\PublicProjectResource;
use App\MoonShine\Resources\Project\RejectedProjectResource;
use App\MoonShine\Resources\Project\DeleteProjectResource;
use Domain\Comment\Models\ProjectComment;
use MoonShine\AssetManager\InlineCss;
use MoonShine\Laravel\Layouts\CompactLayout;
use App\MoonShine\Resources\Comment\DeleteCommentResource;
use App\MoonShine\Resources\Project\UnreadProjectResource;

final class MoonShineLayout extends CompactLayout
{
    protected function assets(): array
    {
        return [
            ...parent::assets(),
            InlineCss::make(<<<'Style'
            :root {
              --radius: 0.15rem;
              --radius-sm: 0.075rem;
              --radius-md: 0.275rem;
              --radius-lg: 0.3rem;
              --radius-xl: 0.4rem;
              --radius-2xl: 0.5rem;
              --radius-3xl: 1rem;
              --radius-full: 9999px;
            }
        Style),
        ];
    }

    protected function menu(): array
    {
        return [
            MenuItem::make(
                static fn () => __('moonshine::ui.resource.users'),
                UserResource::class
            )->icon('users'),
            MenuGroup::make(
                static fn () => __('moonshine::ui.project.title'),
                [
                    MenuGroup::make(
                        static fn () => __('moonshine::ui.projects.title'), [
                            MenuItem::make(
                                static fn () => __('moonshine::ui.projects.unread_title'),
                                UnreadProjectResource::class
                            )->badge(Project::active()->whereNull('read_at')->count()),
                            MenuItem::make(
                                static fn () => __('moonshine::ui.projects.public_title'),
                                PublicProjectResource::class
                            )->badge(Project::active()->whereNotNull('read_at')->count()),
                            MenuItem::make(
                                static fn () => __('moonshine::ui.projects.moderation_title'),
                                ModerationProjectResource::class
                            )->badge(Project::moderation()->count()),
                            MenuItem::make(
                                static fn () => __('moonshine::ui.projects.drafts_title'),
                                DraftProjectResource::class
                            )->badge(Project::drafts()->count()),
                            MenuItem::make(
                                static fn () => __('moonshine::ui.projects.rejected_title'),
                                RejectedProjectResource::class
                            )->badge(Project::rejected()->count()),
                            MenuItem::make(
                                static fn () => __('moonshine::ui.projects.deleted_title'),
                                DeleteProjectResource::class
                            )->badge(Project::remove()->count())
                            ],
                            'book-open'
                        ),
                     MenuGroup::make(
                            static fn () => __('moonshine::ui.resource.comments'),
                            [
                                MenuItem::make(static fn () => __('moonshine::ui.comment.public_title'),
                                CommentResource::class
                                )
                                    ->badge(fn () => (string) ProjectComment::query()->count()),
                                MenuItem::make(static fn () => __('moonshine::ui.comment.deleted_title'),
                                DeleteCommentResource::class
                                )
                                    ->badge(fn () => (string) ProjectComment::query()->whereNotNull('deleted_at')->withTrashed()->count())
                        ],
                        'chat-bubble-left'
                    ),
                ]
            )->icon('document'),
        ];
    }

    /**
     * @param ColorManager $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        $colorManager
            ->primary('#1E96FC')
            ->secondary('#1D8A99')
            ->body('249, 250, 251')
            ->dark('30, 31, 67', 'DEFAULT')
            ->dark('249, 250, 251', 50)
            ->dark('243, 244, 246', 100)
            ->dark('229, 231, 235', 200)
            ->dark('209, 213, 219', 300)
            ->dark('156, 163, 175', 400)
            ->dark('107, 114, 128', 500)
            ->dark('75, 85, 99', 600)
            ->dark('55, 65, 81', 700)
            ->dark('31, 41, 55', 800)
            ->dark('17, 24, 39', 900)
            ->successBg('209, 255, 209')
            ->successText('15, 99, 15')
            ->warningBg('255, 246, 207')
            ->warningText('92, 77, 6')
            ->errorBg('255, 224, 224')
            ->errorText('81, 20, 20')
            ->infoBg('196, 224, 255')
            ->infoText('34, 65, 124');

        $colorManager
            ->body('27, 37, 59', dark: true)
            ->dark('83, 103, 132', 50, dark: true)
            ->dark('74, 90, 121', 100, dark: true)
            ->dark('65, 81, 114', 200, dark: true)
            ->dark('53, 69, 103', 300, dark: true)
            ->dark('48, 61, 93', 400, dark: true)
            ->dark('41, 53, 82', 500, dark: true)
            ->dark('40, 51, 78', 600, dark: true)
            ->dark('39, 45, 69', 700, dark: true)
            ->dark('27, 37, 59', 800, dark: true)
            ->dark('15, 23, 42', 900, dark: true)
            ->successBg('17, 157, 17', dark: true)
            ->successText('178, 255, 178', dark: true)
            ->warningBg('225, 169, 0', dark: true)
            ->warningText('255, 255, 199', dark: true)
            ->errorBg('190, 10, 10', dark: true)
            ->errorText('255, 197, 197', dark: true)
            ->infoBg('38, 93, 205', dark: true)
            ->infoText('179, 220, 255', dark: true);
    }

    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                Head::make([
                    Meta::make()->customAttributes([
                        'name' => 'csrf-token',
                        'content' => csrf_token()
                    ]),
                    Favicon::make()->bodyColor($this->getColorManager()->get('body')),
                    Assets::make(),
                ])
                ->bodyColor($this->getColorManager()->get('body'))
                ->title($this->getPage()->getTitle()),
                Body::make([
                    Wrapper::make([

                        Sidebar::make([
                            Div::make([
                                Div::make([
                                    Logo::make(
                                        $this->getHomeUrl(),
                                        $this->getLogo(),
                                        $this->getLogo(small: true),
                                    )->minimized(),
                                ])->class('menu-heading-logo'),

                                Div::make([
                                    Div::make([
                                        ThemeSwitcher::make(),
                                    ])->class('menu-heading-mode'),

                                    Div::make([
                                        Burger::make(),
                                    ])->class('menu-heading-burger'),
                                ])->class('menu-heading-actions'),
                            ])->class('menu-heading'),

                            Div::make([
                                Menu::make(),
                                When::make(
                                    fn(): bool => $this->isAuthEnabled(),
                                    static fn(): array => [UserProfile::make(withBorder: true)],
                                ),
                            ])->customAttributes([
                                'class' => 'menu',
                                ':class' => "asideMenuOpen && '_is-opened'",
                            ]),
                        ])->collapsed(),

                        Div::make([
                            Flash::make(),

                            Header::make([
                                Breadcrumbs::make($this->getPage()->getBreadcrumbs())->prepend(
                                    $this->getHomeUrl(),
                                    icon: 'home',
                                ),
                                Search::make(),
                                When::make(
                                    fn(): bool => $this->isUseNotifications(),
                                    static fn(): array => [Notifications::make()],
                                ),
                                Locales::make(),
                            ]),

                            Content::make([
                                Components::make(
                                    $this->getPage()->getComponents(),
                                ),
                            ]),

                            Footer::make()
                                ->copyright(static fn(): string
                                    => sprintf(
                                    <<<'HTML'
                                        © 2021-%d Made with ❤️ by
                                            CutCode
                                        HTML,
                                    now()->year,
                            ))->menu([
                                ...$this->getFooterComponent()->getMenu()
                            ])

                        ])->class('layout-page'),
                    ]),
                ])->class('theme-minimalistic'),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
