import { mount } from '@vue/test-utils'
import Posts from '../Posts.vue'
import { server } from '../../mocks/server'

beforeAll(() => server.listen({ onUnhandledRequest: 'error' }))

afterEach(() => server.resetHandlers())

afterAll(() => server.close())


// describe('posts', () => {
//     let wrapper;

//     beforeEach(() => {
//         wrapper = mount(Posts)
//     })

//     it('mock', async () => {
//         const posts = wrapper.findAll(".card")
//         expect(posts.exists()).toBe(true)
//     })
// })

test('posts', async () => {
    // const wrapper = mount(Posts)
    // expect(wrapper.classes('cards')).toBe(false)
})