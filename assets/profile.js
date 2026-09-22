/* ==========================================================================
   Dream · 人设数据（profile.js）
   --------------------------------------------------------------------------
   用途：单一来源描述"站长是谁"。index.html、origin.html、resource.html 都读它渲染，
        不再把头像/定位/标签/社交链接硬编码进 HTML。
   设计：结构与 softwares.js 一致（const 全局对象），方便后续写入 CMS。
   原则：字段缺失时优雅降级 —— 某个标签不填就不渲染，不会把页面搞崩。
   ========================================================================== */

const DREAM_PROFILE = {

    /* ---------- 身份 ---------- */
    name: "Dream",
    /* 昵称后缀（导航/标题用） */
    alias: "主页世界",
    /* 一句话定位 —— 少主 2026-09-20 拍板 */
    tagline: "这是我的第四维度",

    /* ---------- 头像 ----------
       头像可换：后续写入 CMS 后，只需改这里的 avatar 字段。
       当前用站内哆啦A梦素材（1040×1040，清晰度足够）。 */
    avatar: "logo/dlam.png",
    avatarAlt: "Dream",

    /* ---------- 头像池（彩蛋"掷骰子"用，2026-09-21） ----------
       少主拍板建 logo/avatar/ 专用文件夹 —— 作品 logo（mainImage）绝不是头像，
       严禁混入（首页实验页 v15 踩过，断言已锁死）。
       加头像 = 往 logo/avatar/ 丢一张方图 + 下面补一行 { src, title }。
       title 是掷到这张头像时「此刻」行显示的称号，没有就回落随机心情文案。 */
    /* 少主 2026-09-21 拍板：头像只留哆啦一张。
       蓝底白环徽标（dream-mark.png）是**站标**不是头像，已移出头像池、归位到 logo/ 下 ——
       头像位只放"人/角色"的图，站标混进来会让人分不清（v15 的 logo 当头像就是这么踩的）。
       以后加头像：往 logo/avatar/ 丢方图 + 下面补一行 { src, title }，
       骰子会自动卷进去（池子里有 2 张以上才会真的换脸）。 */
    avatars: [
        { src: "logo/avatar/dora-smile.png", title: "元气满满" }
    ],

    /* ---------- 技能词云 ----------
       少主 2026-09-21 拍板：从"分组标签 chip"改成"词云"。
       理由：分组前缀（开发/逆向/兴趣）是给作者自己看的，访客不关心；
            规整的胶囊排布像后台表单，没有呼吸感。
       权重 weight（1-5）：同时决定字号与透明度，模拟"出现次数越多越大"的词云观感。
            weight 越大 = 越核心 = 字越大越实。
       size 是渲染后字号（rem），由 weight 推导，这里显式写出便于微调。
       offset 是星罗棋布用的水平/垂直微偏移台阶（px），
              取 ±1 台阶即可 —— 太大就散架，太小看不出随机感。 */
    skillsCloud: [
        { text: "C# / .NET",   weight: 5, size: 1.45, offset: 0 },
        { text: "内存逆向",     weight: 4, size: 1.28, offset: 2 },
        { text: "WPF",         weight: 4, size: 1.24, offset: -2 },
        { text: "静态反汇编",   weight: 3, size: 1.10, offset: 1 },
        { text: "桌面工具",     weight: 3, size: 1.06, offset: -1 },
        { text: "独立开发",     weight: 3, size: 1.02, offset: 2 },
        { text: "前端",        weight: 2, size: 0.94, offset: -2 },
        { text: "游戏模组",     weight: 2, size: 0.92, offset: 1 }
    ],

    /* ---------- 技能标签组（旧字段，词云上线后不再由首页使用） ----------
       保留原因：origin.html / resource.html 若将来需要分组展示仍可读，
                且避免外部引用突然拿不到数据。新代码请用 skillsCloud。 */
    skills: [
        { group: "开发",  items: ["C# / .NET", "WPF", "前端"] },
        { group: "逆向",  items: ["内存逆向", "静态反汇编"] },
        { group: "兴趣",  items: ["游戏模组", "桌面工具", "独立开发"] }
    ],

    /* ---------- 社交链接 ----------
       数据来源：softwares.js 的 socialLinks（已启用 3 条）。
       这里保留结构，实际渲染优先读 softwares.js，保证"改一处全站生效"。
       若 softwares.js 未加载，则回落这份兜底。 */
    socialFallback: [
        { platform: "bilibili", name: "B站",  icon: "📺", url: "https://space.bilibili.com/2027317299", action: "link" },
        { platform: "email",    name: "邮箱", icon: "✉️", url: "1771232219@qq.com", action: "copy" },
        { platform: "qq",       name: "QQ",   icon: "🐧", url: "1771232219",        action: "copy" }
    ],

    /* ---------- 首页区块开关 ----------
       删除装饰区块（时钟/问候/语录/阅读/音乐）后，这里留下"想留什么"的开关，
       方便日后想加回来时不用翻 HTML。 */
    homeSections: {
        worksWall:  true,   /* 原创作品墙 */
        resourceArea: true, /* 资源区（经典游戏 + 实用工具） */
        milestones: true    /* 项目里程碑 */
    }
};
