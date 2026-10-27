# PVZ 系列 · 子站目录

这里是 `01guo.com/pvz/` 的全部内容。主站首页只留一条横幅指进来，PVZ 的东西都在这个目录里。

## 文件

| 文件 | 是什么 |
|---|---|
| `index.html` | 集群入口页：两张软件卡（Player / 编辑器）+ 一片花田横幅。内容写死，不读 `softwares.js`。 |
| `player.html` | PVZ Player 详情页。按 `id=P733` 从 `../softwares.js` 取版本号、下载地址、截图。 |
| `editor.html` | 关卡编辑器详情页。按 `id=650379` 取同样的东西。 |
| `charge.html` | 花田（项目充能墙）。读 `data/patrons.json` 与 `data/groups.json`。 |
| `bg-lawn.jpg` | 花田页的草坪底图（模糊垫在玻璃底下）。 |
| `data/*.json` | **唯一真身**。CMS「官方群组」「赞助者名单」导出就写这里，PVZ Player 客户端也读这里。 |

## 几条不能破的规矩

1. **不要手改 `data/*.json`**。那是 CMS 导出的产物，手改下次导出就被覆盖。要改去 CMS 改。
2. **不要在页面里再抄一份名单**。以前群号和赞助者有三处手抄，改一处忘两处，线上永远是旧的。
   页面上要显示名单，只允许 `fetch('data/xxx.json')`。
3. **不引 `assets/*.js`**。那套字段名（`people` / `n` / `t`）跟 CMS 契约（`patrons` / `name` / `tier`）
   是两套，两套并存迟早对不上。`charge.html` 里只做一次字段映射。
4. **不放「畅玩机」独立卡片**。畅玩机是 PVZ Player 里的一个窗口，不是单独下载的东西。
   给它一张卡会让人以为要另外下个安装包。
5. **新增页面一律用相对路径**（`data/xxx.json`、`bg-lawn.jpg`），不要写 `/assets/xxx`。

## 以后想单开 pvz01.com

第 5 条就是为了这个：整个目录支持原样搬走。但有两样东西是跟主站共用的，搬的时候要一起带上：

- `../assets/base.css`、`../assets/detail.css` —— 样式
- `../softwares.js` —— 产品库（版本号、下载地址、截图清单），CMS 生成，全站只此一份

也就是说：单开域名时，把 `pvz/` 当站点根，再把 `assets/` 和 `softwares.js` 放到它旁边（根目录），
页面一行都不用改。**故意不做目录内副本** —— 复制一份就要同步两份，那是这个项目吃过最多的亏。
