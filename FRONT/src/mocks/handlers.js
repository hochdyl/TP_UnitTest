import { rest } from 'msw'

export const handlers = [
    // Handles a GET /user request
    rest.get('https://127.0.0.1:8000/api/comment/:postId', (req, res, ctx) => {

        return res(
            // Respond with a 200 status code
            ctx.status(200),
            ctx.json({ id: 3, content: 'content3' })
        )
    }),

    rest.get('https://127.0.0.1:8000/api/post', (req, res, ctx) => {

        return res(
            // Respond with a 200 status code
            ctx.status(200),
            ctx.json({
                comments: [{ id: 3, content: "agergaegeghbzrebzrnbrznb" }, { id: 9, content: "gregreg" }],
                id: 1,
                title: "geagg"
            }, {
                comments: [{ id: 7, content: "jheherhteht" }, { id: 8, content: "fffffffffff" }],
                id: 2,
                title: "geaggzbrebezrbeb"
            })
        )
    }),
]